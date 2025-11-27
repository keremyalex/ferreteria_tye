<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\InventoryMovementDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Mostrar página del carrito
     */
    public function index()
    {
        $categories = Category::whereHas('products', function ($query) {
            $query->whereHas('inventory', function ($q) {
                $q->where('cantidad_actual', '>', 0);
            });
        })->withCount(['products' => function ($query) {
            $query->whereHas('inventory', function ($q) {
                $q->where('cantidad_actual', '>', 0);
            });
        }])->get();

        return Inertia::render('Cart/Index', [
            'categories' => $categories
        ]);
    }

    /**
     * Página de checkout (requiere autenticación)
     */
    public function checkout()
    {
        $categories = Category::whereHas('products', function ($query) {
            $query->whereHas('inventory', function ($q) {
                $q->where('cantidad_actual', '>', 0);
            });
        })->withCount(['products' => function ($query) {
            $query->whereHas('inventory', function ($q) {
                $q->where('cantidad_actual', '>', 0);
            });
        }])->get();

        return Inertia::render('Cart/Checkout', [
            'categories' => $categories
        ]);
    }

    /**
     * Procesar pedido
     */
    public function processOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'cliente.nombre' => 'required|string|max:255',
            'cliente.telefono' => 'required|string|max:20',
            'cliente.email' => 'required|email',
            'metodo_pago' => 'required|in:qr,efectivo',
            'tipo_pago' => 'required|in:contado,credito',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            // Verificar stock disponible
            foreach ($request->items as $item) {
                $product = Product::with('inventory')->find($item['product_id']);
                
                if (!$product || !$product->inventory || $product->inventory->cantidad_actual < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para el producto: {$product->nombre}");
                }
            }

            // Buscar o crear cliente temporal para la orden online
            $cliente = $this->findOrCreateOnlineClient($request->cliente);

            // Configurar campos de crédito si es necesario
            $creditoFields = [];
            if ($request->tipo_pago === 'credito') {
                $mitad = $request->total / 2;
                $creditoFields = [
                    'tipo_pago' => 'credito',
                    'primer_cuota' => $mitad,
                    'segunda_cuota' => $mitad,
                    'fecha_vencimiento_segunda_cuota' => now()->addDays(30),
                    // Si el método de pago es QR, la primera cuota se paga inmediatamente
                    'primer_cuota_pagada' => $request->metodo_pago === 'qr',
                    'fecha_pago_primer_cuota' => $request->metodo_pago === 'qr' ? now() : null,
                ];
            } else {
                $creditoFields = [
                    'tipo_pago' => 'contado',
                ];
            }

            // Crear la orden usando el esquema unificado
            $order = Order::create(array_merge([
                'tipo' => 'online',
                'estado' => 'pendiente',
                'subtotal' => $request->subtotal,
                'total' => $request->total,
                'direccion_facturacion' => [
                    'nombre' => $request->cliente['nombre'],
                    'email' => $request->cliente['email'],
                    'telefono' => $request->cliente['telefono'],
                ],
                'observaciones' => null,
                'metodo_pago' => $request->metodo_pago,
                'estado_pago' => 'pendiente',
                'usuario_id' => auth()->id(),
                'cliente_id' => $cliente->id,
                'vendedor_id' => null, // Las ventas online no tienen vendedor asignado
            ], $creditoFields));

            // Log para debug
            Log::info('Orden creada:', [
                'order_id' => $order->id,
                'numero_orden' => $order->numero_orden,
                'total' => $order->total,
                'metodo_pago' => $order->metodo_pago
            ]);

            // Crear los items de la orden usando el esquema unificado
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                OrderItem::create([
                    'orden_id' => $order->id, // Usar 'orden_id' en lugar de 'order_id'
                    'producto_id' => $item['product_id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio_unitario'],
                    // El modelo calculará automáticamente el total
                ]);
            }

            // NO creamos movimiento de inventario aquí para órdenes online pendientes
            // Se creará cuando la orden sea marcada como pagada

            $orderData = [
                'id' => $order->id,
                'numero_orden' => $order->numero_orden,
                'tipo' => $order->tipo,
                'estado' => $order->estado,
                'total' => $order->total,
                'metodo_pago' => $order->metodo_pago,
            ];

            Log::info('Datos de orden a retornar:', $orderData);

            // Obtener categorías para la vista
            $categories = \App\Models\Category::whereHas('products', function ($query) {
                $query->whereHas('inventory', function ($q) {
                    $q->where('cantidad_actual', '>', 0);
                });
            })->withCount(['products' => function ($query) {
                $query->whereHas('inventory', function ($q) {
                    $q->where('cantidad_actual', '>', 0);
                });
            }])->get();

            return Inertia::render('Cart/Checkout', [
                'categories' => $categories,
                'orderData' => $orderData,
                'message' => 'Pedido procesado exitosamente'
            ]);
        });
    }

    /**
     * Mostrar pedidos a crédito del usuario
     */
    public function misCreditos()
    {
        $categories = Category::whereHas('products', function ($query) {
            $query->whereHas('inventory', function ($q) {
                $q->where('cantidad_actual', '>', 0);
            });
        })->withCount(['products' => function ($query) {
            $query->whereHas('inventory', function ($q) {
                $q->where('cantidad_actual', '>', 0);
            });
        }])->get();

        $creditOrders = Order::where('usuario_id', auth()->id())
            ->where('tipo_pago', 'credito')
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Shop/MisCreditos', [
            'categories' => $categories,
            'creditOrders' => $creditOrders
        ]);
    }

    public function pagarCuota(Request $request, $orderId)
    {
        $request->validate([
            'cuota' => 'required|in:primera,segunda',
            'metodo_pago' => 'required|in:qr,efectivo'
        ]);

        $order = Order::where('usuario_id', Auth::id())
            ->where('id', $orderId)
            ->where('tipo_pago', 'credito')
            ->firstOrFail();

        // Verificar que la cuota no esté ya pagada
        $cuotaPagada = $request->cuota === 'primera' ? $order->primer_cuota_pagada : $order->segunda_cuota_pagada;
        if ($cuotaPagada) {
            return redirect()->route('client.credits')->with('error', 'Esta cuota ya está pagada.');
        }

        // Verificar que si es segunda cuota, la primera debe estar pagada
        if ($request->cuota === 'segunda' && !$order->primer_cuota_pagada) {
            return redirect()->route('client.credits')->with('error', 'Debe pagar primero la primera cuota.');
        }

        if ($request->metodo_pago === 'efectivo') {
            // Para efectivo, marcar directamente como pagado
            if ($request->cuota === 'primera') {
                $order->update([
                    'primer_cuota_pagada' => true,
                    'fecha_pago_primer_cuota' => now(),
                ]);
                $mensaje = 'Primera cuota pagada en efectivo';
            } else {
                $order->update([
                    'segunda_cuota_pagada' => true,
                    'fecha_pago_segunda_cuota' => now(),
                ]);
                $mensaje = 'Segunda cuota pagada en efectivo - ¡Crédito completado!';
            }
            
            return redirect()->route('client.credits')->with('success', $mensaje);
        } else {
            // Para QR, redirigir al generador de QR con parámetros de cuota
            $amount = $request->cuota === 'primera' ? $order->primer_cuota : $order->segunda_cuota;
            $paymentType = $request->cuota . '_cuota';
            $description = "Pago {$request->cuota} cuota - Pedido #{$order->numero_orden}";
            
            return redirect()->route('qr.generate')->withInput([
                'order_id' => $order->id,
                'payment_type' => $paymentType,
                'amount' => $amount,
                'description' => $description
            ]);
        }
    }

    /**
     * Buscar o crear un cliente temporal para órdenes online
     */
    private function findOrCreateOnlineClient($clienteData)
    {
        // Buscar cliente existente por teléfono (más confiable para órdenes online)
        $cliente = \App\Models\Client::where('telf', $clienteData['telefono'])->first();

        if (!$cliente) {
            // Crear cliente temporal
            $cliente = \App\Models\Client::create([
                'nombre' => $clienteData['nombre'],
                'telf' => $clienteData['telefono'],
                'ci' => null, // Para órdenes online, no tenemos CI inicialmente
                'nit' => null, // Para órdenes online, no tenemos NIT inicialmente
            ]);
        } else {
            // Actualizar información si ha cambiado
            $cliente->update([
                'nombre' => $clienteData['nombre'],
            ]);
        }

        return $cliente;
    }
}
