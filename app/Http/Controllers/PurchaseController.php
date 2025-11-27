<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\InventoryMovementDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view.purchases')->only(['index', 'show']);
        $this->middleware('can:create.purchases')->only(['create', 'store']);
        $this->middleware('can:edit.purchases')->only(['edit', 'update']);
        $this->middleware('can:delete.purchases')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'purchaseDetails'])
            ->select('purchases.*');

        // Filtros de búsqueda
        if ($request->filled('search') && !empty(trim($request->search))) {
            $query->search(trim($request->search));
        }

        // Filtro por proveedor
        if ($request->filled('supplier') && !empty($request->supplier)) {
            $query->where('supplier_id', $request->supplier);
        }

        // Filtro por rango de fechas
        if ($request->filled('date_start') && !empty($request->date_start)) {
            $query->where('fecha', '>=', $request->date_start);
        }

        if ($request->filled('date_end') && !empty($request->date_end)) {
            $query->where('fecha', '<=', $request->date_end);
        }

        // Ordenar resultados
        $sortField = $request->get('sort', 'fecha');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $purchases = $query->paginate(15)->withQueryString();

        // Obtener proveedores para filtros
        $suppliers = Supplier::orderBy('nombre_empresa')->get();

        // Estadísticas
        $stats = [
            'total_purchases' => Purchase::count(),
            'total_amount' => Purchase::sum('monto_total') ?? 0,
            'this_month_purchases' => Purchase::whereMonth('fecha', Carbon::now()->month)
                                           ->whereYear('fecha', Carbon::now()->year)
                                           ->count(),
            'this_month_amount' => Purchase::whereMonth('fecha', Carbon::now()->month)
                                          ->whereYear('fecha', Carbon::now()->year)
                                          ->sum('monto_total') ?? 0,
        ];

        return Inertia::render('Purchases/Index', [
            'purchases' => $purchases,
            'suppliers' => $suppliers,
            'filters' => [
                'search' => $request->get('search', ''),
                'supplier' => $request->get('supplier', ''),
                'date_start' => $request->get('date_start', ''),
                'date_end' => $request->get('date_end', ''),
                'sort' => $sortField,
                'direction' => $sortDirection
            ],
            'stats' => $stats
        ]);
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('nombre_empresa')->get();
        $products = Product::with(['category', 'measurement'])->orderBy('nombre')->get();
        
        return Inertia::render('Purchases/Create', [
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'supplier_id' => 'required|exists:suppliers,id',
            'observaciones' => 'nullable|string|max:500',
            'productos' => 'required|array|min:1',
            'productos.*.product_id' => 'required|exists:products,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
        ], [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha debe ser válida.',
            'hora.required' => 'La hora es obligatoria.',
            'hora.date_format' => 'La hora debe tener el formato HH:MM.',
            'supplier_id.required' => 'El proveedor es obligatorio.',
            'supplier_id.exists' => 'El proveedor seleccionado no existe.',
            'productos.required' => 'Debe agregar al menos un producto.',
            'productos.*.product_id.required' => 'El producto es obligatorio.',
            'productos.*.product_id.exists' => 'El producto seleccionado no existe.',
            'productos.*.cantidad.required' => 'La cantidad es obligatoria.',
            'productos.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'productos.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'productos.*.precio.required' => 'El precio es obligatorio.',
            'productos.*.precio.numeric' => 'El precio debe ser un número.',
            'productos.*.precio.min' => 'El precio debe ser mayor o igual a 0.',
        ]);

        // Calcular monto total
        $montoTotal = 0;
        foreach ($validated['productos'] as $producto) {
            $montoTotal += $producto['cantidad'] * $producto['precio'];
        }

        // Crear compra
        $purchase = Purchase::create([
            'nro' => (new Purchase())->generateNumber(),
            'fecha' => $validated['fecha'],
            'hora' => $validated['hora'],
            'supplier_id' => $validated['supplier_id'],
            'observaciones' => $validated['observaciones'],
            'monto_total' => $montoTotal,
        ]);

        // Crear detalles de compra (SIN actualizar inventario)
        foreach ($validated['productos'] as $producto) {
            // Crear detalle de compra
            PurchaseDetail::create([
                'purchase_id' => $purchase->id,
                'product_id' => $producto['product_id'],
                'cantidad' => $producto['cantidad'],
                'precio' => $producto['precio'],
            ]);

            // Actualizar precios del producto automáticamente
            $productModel = \App\Models\Product::find($producto['product_id']);
            $productModel->actualizarPrecioConCompra($producto['precio']);
        }

        return redirect()->route('purchases.index')
            ->with('success', 'Compra registrada exitosamente. Use "Recibir Mercancía" para actualizar el inventario.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'purchaseDetails.product.category', 'purchaseDetails.product.measurement']);
        
        return Inertia::render('Purchases/Show', [
            'purchase' => $purchase,
        ]);
    }

    /**
     * Mostrar formulario para recibir mercancía
     */
    public function recibir(Purchase $purchase)
    {
        // Solo se puede recibir si está pendiente
        if (!$purchase->isPendiente()) {
            return redirect()->route('purchases.show', $purchase)
                ->with('error', 'Esta compra ya fue procesada.');
        }

        $purchase->load(['supplier', 'purchaseDetails.product.category', 'purchaseDetails.product.measurement']);
        
        return Inertia::render('Purchases/Recibir', [
            'purchase' => $purchase,
        ]);
    }

    /**
     * Procesar recepción de mercancía
     */
    public function procesarRecepcion(Request $request, Purchase $purchase)
    {
        // Validar que la compra esté pendiente
        if (!$purchase->isPendiente()) {
            return redirect()->route('purchases.show', $purchase)
                ->with('error', 'Esta compra ya fue procesada.');
        }

        // Validar datos de recepción
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_details,id',
            'items.*.cantidad_recibida' => 'required|integer|min:0',
            'items.*.observaciones' => 'nullable|string|max:500',
        ], [
            'items.required' => 'Debe especificar las cantidades recibidas.',
            'items.*.cantidad_recibida.required' => 'La cantidad recibida es obligatoria.',
            'items.*.cantidad_recibida.integer' => 'La cantidad debe ser un número entero.',
            'items.*.cantidad_recibida.min' => 'La cantidad debe ser mayor o igual a 0.',
        ]);

        return DB::transaction(function () use ($request, $purchase, $validated) {
            $observacionesMovimiento = [];

            // Crear movimiento de inventario tipo 'entrada'
            $movimiento = InventoryMovement::create([
                'tipo' => 'entrada',
                'fecha' => now(),
                'referencia' => $purchase->nro,
                'observaciones' => "Recepción de compra #{$purchase->nro} - Proveedor: {$purchase->supplier->nombre_empresa}",
                'estado' => 'aplicado',
                'usuario_id' => auth()->id(),
            ]);

            // Procesar cada item
            foreach ($validated['items'] as $itemData) {
                $purchaseDetail = PurchaseDetail::find($itemData['id']);
                $cantidadRecibida = $itemData['cantidad_recibida'];
                $observaciones = $itemData['observaciones'] ?? null;

                // Actualizar purchase_detail
                $purchaseDetail->marcarComoRecibido($cantidadRecibida, $observaciones);

                // Si se recibió algo, crear detalle de movimiento y actualizar inventario
                if ($cantidadRecibida > 0) {
                    // Crear detalle del movimiento
                    InventoryMovementDetail::create([
                        'movimiento_id' => $movimiento->id,
                        'producto_id' => $purchaseDetail->product_id,
                        'cantidad' => $cantidadRecibida,
                        'precio_unitario' => $purchaseDetail->precio,
                        'observaciones' => $observaciones,
                    ]);

                    // Actualizar inventario
                    $inventory = Inventory::where('producto_id', $purchaseDetail->product_id)->first();
                    if ($inventory) {
                        $inventory->sumarStock($cantidadRecibida);
                    } else {
                        // Crear registro de inventario si no existe
                        Inventory::create([
                            'producto_id' => $purchaseDetail->product_id,
                            'cantidad_actual' => $cantidadRecibida,
                            'cantidad_minima' => 10, // Valor por defecto
                            'cantidad_maxima' => 1000, // Valor por defecto
                            'precio_venta' => $purchaseDetail->product->precio_venta ?? $purchaseDetail->precio * 1.3 // 30% de margen por defecto
                        ]);
                    }
                }

                // Recopilar observaciones para el log
                if ($cantidadRecibida !== $purchaseDetail->cantidad) {
                    $producto = $purchaseDetail->product->nombre;
                    $observacionesMovimiento[] = "$producto: Pedido {$purchaseDetail->cantidad}, Recibido $cantidadRecibida";
                }
            }

            // Actualizar estado de la compra
            $purchase->actualizarEstadoBasadoEnItems();

            // Agregar observaciones de diferencias al movimiento
            if (!empty($observacionesMovimiento)) {
                $movimiento->update([
                    'observaciones' => $movimiento->observaciones . "\n\nDiferencias:\n" . implode("\n", $observacionesMovimiento)
                ]);
            }

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Recepción procesada exitosamente. El inventario ha sido actualizado.');
        });
    }

    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::orderBy('nombre_empresa')->get();
        $products = Product::with(['category', 'measurement'])->orderBy('nombre')->get();
        $purchase->load(['purchaseDetails.product']);
        
        return Inertia::render('Purchases/Edit', [
            'purchase' => $purchase,
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'supplier_id' => 'required|exists:suppliers,id',
            'observaciones' => 'nullable|string|max:500',
        ], [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha debe ser válida.',
            'hora.required' => 'La hora es obligatoria.',
            'hora.date_format' => 'La hora debe tener el formato HH:MM.',
            'supplier_id.required' => 'El proveedor es obligatorio.',
            'supplier_id.exists' => 'El proveedor seleccionado no existe.',
        ]);

        $purchase->update($validated);

        return redirect()->route('purchases.index')
            ->with('success', 'Compra actualizada exitosamente.');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Compra eliminada exitosamente.');
    }
}