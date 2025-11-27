<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\QrTransaction;
use App\Services\PagoFacilService;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class QrPaymentController extends Controller
{
    private $pagoFacilService;

    public function __construct(PagoFacilService $pagoFacilService)
    {
        $this->pagoFacilService = $pagoFacilService;
    }

    /**
     * Generar QR para una orden
     */
    public function generateQR(Request $request)
    {
        Log::info('🎯 GenerateQR llamado', [
            'method' => $request->method(),
            'all_input' => $request->all(),
            'query' => $request->query(),
            'old_input' => old()
        ]);

        // Si es GET (redirección desde pagarCuota), mostrar página de QR
        if ($request->isMethod('GET')) {
            // Combinar datos de POST y GET para manejar redirecciones con withInput()
            $allInput = array_merge($request->query(), $request->input(), old());
            
            Log::info('📋 Datos combinados para GET', $allInput);
            
            try {
                $validator = \Illuminate\Support\Facades\Validator::make($allInput, [
                    'order_id' => 'required|exists:orders,id',
                    'payment_type' => 'nullable|in:total,primera_cuota,segunda_cuota',
                    'amount' => 'nullable|numeric|min:0',
                    'description' => 'nullable|string|max:255'
                ]);
                
                if ($validator->fails()) {
                    throw new \Illuminate\Validation\ValidationException($validator);
                }
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('❌ Error de validación en generateQR', [
                    'errors' => $e->errors(),
                    'input' => $allInput
                ]);
                return redirect()->route('client.credits')->withErrors($e->errors());
            }

            $orderId = $allInput['order_id'] ?? null;
            if (!$orderId) {
                Log::error('❌ No se encontró order_id en los datos');
                return redirect()->route('client.credits')->withErrors(['error' => 'ID de orden no encontrado']);
            }

            $order = Order::with(['items.product'])->find($orderId);
            if (!$order) {
                Log::error('❌ Orden no encontrada', ['order_id' => $orderId]);
                return redirect()->route('client.credits')->withErrors(['error' => 'Orden no encontrada']);
            }

            // Verificar que la orden pertenece al usuario autenticado
            if ($order->usuario_id !== auth()->id()) {
                Log::warning('❌ Usuario no autorizado para orden', [
                    'user_id' => auth()->id(),
                    'order_user_id' => $order->usuario_id
                ]);
                return redirect()->route('client.credits')->withErrors([
                    'error' => 'No autorizado para esta orden'
                ]);
            }

            // Preparar datos para la vista
            $paymentType = $allInput['payment_type'] ?? 'total';
            $amount = $allInput['amount'] ?? $order->total;
            $description = $allInput['description'] ?? "Pago orden #{$order->numero_orden}";

            Log::info('✅ Renderizando vista QR', [
                'order_id' => $order->id,
                'payment_type' => $paymentType,
                'amount' => $amount,
                'description' => $description
            ]);

            // Renderizar página de QR con Inertia
            return Inertia::render('QrPayment/Show', [
                'order' => $order,
                'paymentType' => $paymentType,
                'amount' => $amount,
                'description' => $description
            ]);
        }
        
        // Combinar datos de POST y GET para manejar redirecciones con withInput()
        $allInput = array_merge($request->query(), $request->input());
        $request->merge($allInput);
        
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'client_document_id' => 'nullable|string|max:20',
            'payment_type' => 'nullable|in:total,primera_cuota,segunda_cuota',
            'amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:255'
        ]);

        try {
            $order = Order::with(['items.product'])->findOrFail($request->order_id);

            // Verificar que la orden pertenece al usuario autenticado
            if ($order->usuario_id !== auth()->id()) {
                return redirect()->back()->withErrors([
                    'error' => 'No autorizado para esta orden'
                ]);
            }

            // Determinar tipo de pago y monto
            $paymentType = $request->payment_type ?? 'total';
            $description = $request->description ?? "Pago orden #{$order->numero_orden}";
            
            if ($paymentType === 'total') {
                $amount = $order->total;
                // Verificar que la orden no tenga ya un QR pendiente o pagado para pago total
                $existingQR = QrTransaction::where('order_id', $order->id)
                    ->where('payment_type', 'total')
                    ->whereIn('status', ['pending', 'paid'])
                    ->first();
            } else {
                // Para cuotas, usar el monto especificado
                $amount = $request->amount ?? ($paymentType === 'primera_cuota' ? $order->primer_cuota : $order->segunda_cuota);
                
                // Verificar que no existe ya un QR para esta cuota específica
                $existingQR = QrTransaction::where('order_id', $order->id)
                    ->where('payment_type', $paymentType)
                    ->whereIn('status', ['pending', 'paid'])
                    ->first();
                    
                // Validaciones específicas para cuotas
                if ($paymentType === 'primera_cuota' && $order->primer_cuota_pagada) {
                    return redirect()->back()->withErrors(['error' => 'La primera cuota ya está pagada']);
                }
                if ($paymentType === 'segunda_cuota') {
                    if ($order->segunda_cuota_pagada) {
                        return redirect()->back()->withErrors(['error' => 'La segunda cuota ya está pagada']);
                    }
                    if (!$order->primer_cuota_pagada) {
                        return redirect()->back()->withErrors(['error' => 'Debe pagar primero la primera cuota']);
                    }
                }
            }

            if ($existingQR) {
                if ($existingQR->isPaid()) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Esta orden ya fue pagada'
                        ], 400);
                    }
                    return redirect()->back()->withErrors([
                        'error' => 'Esta orden ya fue pagada'
                    ]);
                }

                if ($existingQR->isPending() && !$existingQR->isExpired()) {
                    // Retornar QR existente si aún es válido
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => true,
                            'qr_transaction' => $existingQR,
                            'qr_data' => $existingQR->qr_data
                        ]);
                    }
                    return redirect()->back()->with([
                        'success' => true,
                        'qr_transaction' => $existingQR,
                        'qr_data' => $existingQR->qr_data
                    ]);
                }
            }

            return DB::transaction(function () use ($order, $request, $amount, $paymentType, $description) {
                // Usar monto real o de sandbox según el entorno
                $finalAmount = config('services.pagofacil.environment') === 'sandbox' ? 0.20 : $amount;
                
                $qrTransaction = QrTransaction::create([
                    'payment_number' => QrTransaction::generatePaymentNumber(),
                    'order_id' => $order->id,
                    'client_name' => $order->direccion_facturacion['nombre'],
                    'client_email' => $order->direccion_facturacion['email'],
                    'client_phone' => $order->direccion_facturacion['telefono'],
                    'client_document_id' => $request->client_document_id ?: '12345678',
                    'document_type' => 1, // CI
                    'client_code' => (string) auth()->id(),
                    'amount' => $finalAmount,
                    'currency' => 2, // BOB
                    'payment_method' => 4, // QR
                    'payment_type' => $paymentType,
                    'description' => $description,
                    'status' => 'pending'
                ]);

                // Para sandbox usar URL de prueba válida que sea accesible desde internet
                $callbackUrl = env('APP_ENV') === 'local' 
                    ? 'https://www.tecnoweb.org.bo' // URL de prueba válida y accesible
                    : route('qr.callback', ['payment_number' => $qrTransaction->payment_number]);
                    
                Log::info('URL de callback generada', ['callback_url' => $callbackUrl]);

                // Preparar datos para PagoFácil
                $orderData = $this->pagoFacilService->buildOrderData(
                    $order, 
                    $qrTransaction, 
                    $callbackUrl
                );

                // Generar QR con PagoFácil
                $result = $this->pagoFacilService->generateQR($orderData);

                if (!$result['success']) {
                    // Eliminar transacción si falla
                    $qrTransaction->delete();
                    
                    return response()->json([
                        'success' => false,
                        'error' => 'Error al generar QR: ' . $result['error']
                    ], 500);
                }

                // Extraer el QR Base64 de la respuesta (no guardarlo en BD)
                $qrBase64 = $result['qr_base64'] ?? null;
                $generateResponse = isset($result['data']) ? $this->sanitizeResponseData($result['data']) : null;
                
                // Actualizar transacción sin el QR Base64 (no es necesario guardarlo)
                $qrTransaction->update([
                    'transaction_id' => $result['transaction_id'] ?? null,
                    'access_token' => $result['access_token'] ?? null,
                    'generate_response' => $generateResponse,
                    'expires_at' => now()->addMinutes(15) // QR válido por 15 minutos
                ]);

                Log::info('QR generado exitosamente', [
                    'order_id' => $order->id,
                    'payment_number' => $qrTransaction->payment_number,
                    'transaction_id' => $qrTransaction->transaction_id,
                    'qr_generated' => !empty($qrBase64)
                ]);

                // Detectar si es petición AJAX o Inertia
                if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
                    return response()->json([
                        'success' => true,
                        'qr_transaction' => $qrTransaction,
                        'qr_data' => $qrBase64,
                        'expires_at' => $qrTransaction->expires_at,
                        'payment_number' => $qrTransaction->payment_number
                    ]);
                }

                // Para Inertia.js, usar redirect with data
                return redirect()->back()->with([
                    'success' => true,
                    'qr_transaction' => $qrTransaction,
                    'qr_data' => $qrBase64,
                    'expires_at' => $qrTransaction->expires_at,
                    'payment_number' => $qrTransaction->payment_number
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error al generar QR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error interno del servidor'
                ], 500);
            }

            return redirect()->back()->withErrors([
                'error' => 'Error interno del servidor'
            ]);
        }
    }

    /**
     * Verificar estado del pago
     */
    public function verifyPayment(Request $request)
    {
        Log::info('🔍 INICIO VERIFICACIÓN MANUAL DE PAGO', [
            'request_data' => $request->all(),
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'ip' => $request->ip()
        ]);
        
        $request->validate([
            'payment_number' => 'required|exists:qr_transactions,payment_number'
        ]);

        try {
            $qrTransaction = QrTransaction::where('payment_number', $request->payment_number)
                ->with('order')
                ->first();

            if (!$qrTransaction) {
                Log::warning('❌ Transacción no encontrada', [
                    'payment_number' => $request->payment_number
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Transacción no encontrada'
                ], 404);
            }

            Log::info('📋 Datos de transacción encontrada', [
                'payment_number' => $qrTransaction->payment_number,
                'current_status' => $qrTransaction->status,
                'is_paid' => $qrTransaction->isPaid(),
                'is_expired' => $qrTransaction->isExpired(),
                'order_id' => $qrTransaction->order_id
            ]);

            // Si ya está pagado, retornar estado
            if ($qrTransaction->isPaid()) {
                Log::info('✅ Transacción ya está pagada', [
                    'payment_number' => $qrTransaction->payment_number,
                    'paid_at' => $qrTransaction->paid_at
                ]);
                
                return response()->json([
                    'success' => true,
                    'status' => 'paid',
                    'paid_at' => $qrTransaction->paid_at
                ]);
            }

            // Si expiró, marcar como expirado
            if ($qrTransaction->isExpired()) {
                Log::info('⏰ Transacción expirada', [
                    'payment_number' => $qrTransaction->payment_number
                ]);
                
                $qrTransaction->markAsExpired();
                return response()->json([
                    'success' => true,
                    'status' => 'expired'
                ]);
            }

            // Verificar con PagoFácil usando payment_number
            if ($qrTransaction->payment_number) {
                Log::info('🚀 Iniciando verificación con PagoFácil', [
                    'payment_number' => $qrTransaction->payment_number
                ]);
                
                $result = $this->pagoFacilService->verifyPayment($qrTransaction->payment_number);
                
                Log::info('📥 Resultado de verificación PagoFácil', [
                    'payment_number' => $qrTransaction->payment_number,
                    'verification_success' => $result['success'],
                    'full_result' => $result
                ]);
                
                // Guardar respuesta de verificación
                $qrTransaction->update([
                    'verify_response' => $result['data'] ?? null
                ]);

                if ($result['success']) {
                    $status = $result['status'];
                    $rawStatus = $result['raw_status'] ?? 'N/A';
                    
                    Log::info('🎯 Estado de pago verificado', [
                        'payment_number' => $qrTransaction->payment_number,
                        'mapped_status' => $status,
                        'raw_status' => $rawStatus
                    ]);
                    
                    // Si el pago fue confirmado (incluyendo estado 5 - Revisión)
                    if (in_array($status, ['paid'])) {
                        Log::info('💰 PAGO CONFIRMADO - Procesando orden', [
                            'payment_number' => $qrTransaction->payment_number,
                            'raw_status' => $rawStatus
                        ]);
                        
                        $this->processPaidOrder($qrTransaction);
                        
                        return response()->json([
                            'success' => true,
                            'status' => 'paid',
                            'paid_at' => $qrTransaction->paid_at,
                            'raw_payment_status' => $rawStatus
                        ]);
                    }
                    
                    // Si está cancelado
                    if ($status === 'cancelled') {
                        Log::info('❌ Pago cancelado', [
                            'payment_number' => $qrTransaction->payment_number
                        ]);
                        
                        $qrTransaction->markAsExpired();
                        return response()->json([
                            'success' => true,
                            'status' => 'expired'
                        ]);
                    }
                    
                    Log::info('⏳ Pago aún pendiente', [
                        'payment_number' => $qrTransaction->payment_number,
                        'status' => $status,
                        'raw_status' => $rawStatus
                    ]);
                } else {
                    Log::warning('⚠️ Error en verificación PagoFácil', [
                        'payment_number' => $qrTransaction->payment_number,
                        'error' => $result['error'] ?? 'Error desconocido'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'status' => 'pending'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al verificar pago', [
                'error' => $e->getMessage(),
                'payment_number' => $request->payment_number
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al verificar el pago'
            ], 500);
        }
    }

    /**
     * Callback de PagoFácil
     */
    public function callback(Request $request, $payment_number)
    {
        Log::info('Callback recibido de PagoFácil', [
            'payment_number' => $payment_number,
            'data' => $request->all()
        ]);

        try {
            $qrTransaction = QrTransaction::where('payment_number', $payment_number)->first();

            if (!$qrTransaction) {
                Log::warning('Callback para transacción inexistente', [
                    'payment_number' => $payment_number
                ]);
                return response()->json(['status' => 'error', 'message' => 'Transacción no encontrada'], 404);
            }

            // Guardar datos del callback
            $qrTransaction->update([
                'callback_data' => $request->all()
            ]);

            // Verificar si el pago fue exitoso según el callback
            $status = $request->input('status') ?? $request->input('State') ?? 'unknown';
            
            if (in_array(strtolower($status), ['paid', 'completed', 'success', 'pagado'])) {
                $this->processPaidOrder($qrTransaction);
                
                Log::info('Pago confirmado por callback', [
                    'payment_number' => $payment_number,
                    'status' => $status
                ]);

                return response()->json(['status' => 'success', 'message' => 'Pago procesado']);
            }

            Log::info('Callback recibido pero pago no confirmado', [
                'payment_number' => $payment_number,
                'status' => $status
            ]);

            return response()->json(['status' => 'pending', 'message' => 'Pago pendiente']);

        } catch (\Exception $e) {
            Log::error('Error en callback', [
                'error' => $e->getMessage(),
                'payment_number' => $payment_number
            ]);

            return response()->json(['status' => 'error', 'message' => 'Error interno'], 500);
        }
    }

    /**
     * Procesar orden pagada
     */
    private function processPaidOrder(QrTransaction $qrTransaction)
    {
        if ($qrTransaction->isPaid()) {
            return; // Ya procesado
        }

        DB::transaction(function () use ($qrTransaction) {
            // Marcar transacción como pagada
            $qrTransaction->markAsPaid();

            $order = $qrTransaction->order;

            // Manejar diferentes tipos de pago
            if ($qrTransaction->payment_type === 'primera_cuota') {
                // Marcar primera cuota como pagada
                $order->update([
                    'primer_cuota_pagada' => true,
                    'fecha_pago_primer_cuota' => now(),
                ]);
                
                Log::info('Primera cuota pagada', [
                    'order_id' => $order->id,
                    'payment_number' => $qrTransaction->payment_number
                ]);
                
            } elseif ($qrTransaction->payment_type === 'segunda_cuota') {
                // Marcar segunda cuota como pagada
                $order->update([
                    'segunda_cuota_pagada' => true,
                    'fecha_pago_segunda_cuota' => now(),
                ]);
                
                // Si ambas cuotas están pagadas, marcar orden como completamente pagada
                if ($order->primer_cuota_pagada) {
                    $order->update([
                        'estado_pago' => 'pagado'
                    ]);
                }
                
                Log::info('Segunda cuota pagada', [
                    'order_id' => $order->id,
                    'payment_number' => $qrTransaction->payment_number,
                    'credito_completo' => $order->primer_cuota_pagada
                ]);
                
            } else {
                // Pago total (contado)
                $order->update([
                    'estado' => 'confirmado',
                    'estado_pago' => 'pagado'
                ]);
                
                Log::info('Pago total completado', [
                    'order_id' => $order->id,
                    'payment_number' => $qrTransaction->payment_number
                ]);
            }

            // Aplicar al inventario solo para pagos totales o cuando se complete el crédito
            if ($qrTransaction->payment_type === 'total' || 
                ($qrTransaction->payment_type === 'segunda_cuota' && $order->primer_cuota_pagada)) {
                $this->applyToInventory($order);
            } elseif ($qrTransaction->payment_type === 'primera_cuota') {
                // Para primera cuota, solo confirmar la orden pero no aplicar al inventario aún
                $order->update(['estado' => 'confirmado']);
            }
        });
    }

    /**
     * Aplicar venta al inventario
     */
    private function applyToInventory(Order $order)
    {
        Log::info('🏬 Aplicando venta al inventario', [
            'order_id' => $order->id,
            'numero_orden' => $order->numero_orden
        ]);

        // Crear movimiento de salida automático
        $movement = InventoryMovement::create([
            'tipo' => 'salida',
            'fecha' => now(),
            'referencia' => "Venta #{$order->numero_orden}",
            'observaciones' => "Venta " . ($order->isPresencial() ? 'presencial' : 'online'),
            'estado' => 'pendiente',
            'usuario_id' => $order->vendedor_id ?? $order->usuario_id,
        ]);

        Log::info('📦 Creando detalles de movimiento de inventario', [
            'movement_id' => $movement->id,
            'items_count' => $order->items->count()
        ]);

        foreach ($order->items as $item) {
            $movement->details()->create([
                'producto_id' => $item->producto_id,
                'cantidad' => $item->cantidad,
                'precio_unitario' => $item->precio,
                'observaciones' => "Venta de {$item->cantidad} unidades",
            ]);

            Log::info('📋 Detalle agregado', [
                'producto_id' => $item->producto_id,
                'cantidad' => $item->cantidad,
                'precio' => $item->precio
            ]);
        }

        // Aplicar al inventario
        try {
            $movement->aplicar();
            Log::info('✅ Movimiento aplicado al inventario exitosamente', [
                'movement_id' => $movement->id
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error al aplicar movimiento al inventario', [
                'error' => $e->getMessage(),
                'movement_id' => $movement->id
            ]);
            throw $e;
        }
    }

    /**
     * Sanitizar datos de respuesta para PostgreSQL
     */
    private function sanitizeResponseData($data)
    {
        if (!$data) {
            return null;
        }
        
        // Crear una copia limpia
        $cleanData = is_array($data) ? $data : [];
        
        // Procesar cada campo recursivamente
        array_walk_recursive($cleanData, function(&$item) {
            if (is_string($item)) {
                // Limpiar caracteres problemáticos
                $item = str_replace(["\r", "\n", "\t", "\\", "\x00"], '', $item);
                // Asegurar UTF-8 válido
                $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                
                // Si es muy largo, truncar
                if (strlen($item) > 10000) {
                    $item = substr($item, 0, 10000) . '...';
                }
            }
        });
        
        // Remover qrBase64 para evitar problemas de tamaño
        if (isset($cleanData['values']['qrBase64'])) {
            unset($cleanData['values']['qrBase64']);
        }
        
        return $cleanData;
    }
}
