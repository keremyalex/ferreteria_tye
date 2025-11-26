<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Client;
use App\Models\Product;
use App\Models\User;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['user', 'client', 'seller', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Orders/Index', [
            'orders' => $orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::select('id', 'nombre', 'ci', 'nit', 'telf')->get();
        $products = Product::with(['category', 'measurement', 'inventory'])
            ->whereHas('inventory', function($query) {
                $query->where('cantidad_actual', '>', 0);
            })
            ->get()
            ->map(function($product) {
                $product->stock = $product->inventory?->cantidad_actual ?? 0;
                return $product;
            });

        return Inertia::render('Orders/Create', [
            'clients' => $clients,
            'products' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'tipo' => 'required|in:online,presencial',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,contraentrega',
            'estado_pago' => 'required|in:pendiente,pagado',
            'notas' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:products,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio' => 'required|numeric|min:0',
        ];

        // Validaciones condicionales según el tipo
        if ($request->tipo === 'online') {
            $rules['usuario_id'] = 'required|exists:users,id';
            $rules['direccion_facturacion'] = 'nullable|array';
        } else {
            $rules['cliente_id'] = 'required|exists:clients,id';
        }

        $validated = $request->validate($rules);

        return DB::transaction(function () use ($validated, $request) {
            // Crear la orden
            $orderData = [
                'tipo' => $validated['tipo'],
                'subtotal' => $validated['subtotal'],
                'total' => $validated['total'],
                'metodo_pago' => $validated['metodo_pago'],
                'estado_pago' => $validated['estado_pago'],
                'observaciones' => $validated['notas'],
            ];

            if ($validated['tipo'] === 'online') {
                $orderData['usuario_id'] = $validated['usuario_id'];
                $orderData['direccion_facturacion'] = $validated['direccion_facturacion'] ?? null;
                $orderData['estado'] = 'pendiente';
            } else {
                $orderData['cliente_id'] = $validated['cliente_id'];
                $orderData['vendedor_id'] = Auth::id();
                $orderData['estado'] = $validated['estado_pago'] === 'pagado' ? 'completado' : 'pendiente';
            }

            $order = Order::create($orderData);

            // Crear los items
            foreach ($validated['items'] as $item) {
                $order->items()->create([
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                    // El total se calcula automáticamente en el modelo
                ]);
            }

            // Si es venta presencial y está pagada, aplicar al inventario inmediatamente
            if ($order->isPresencial() && $order->estado_pago === 'pagado') {
                $this->applyToInventory($order);
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Venta creada exitosamente.');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'client', 'seller', 'items.product.category', 'items.product.measurement']);

        return Inertia::render('Orders/Show', [
            'order' => $order
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'client', 'seller', 'items.product']);
        
        $clients = Client::select('id', 'nombre', 'ci', 'nit', 'telf')->get();
        $products = Product::with(['category', 'measurement', 'inventory'])
            ->whereHas('inventory', function($query) {
                $query->where('cantidad_actual', '>', 0);
            })
            ->get()
            ->map(function($product) {
                $product->stock = $product->inventory?->cantidad_actual ?? 0;
                return $product;
            });

        return Inertia::render('Orders/Edit', [
            'order' => $order,
            'clients' => $clients,
            'products' => $products
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'cliente_id' => 'sometimes|exists:clients,id',
            'metodo_pago' => 'sometimes|string|in:efectivo,tarjeta,transferencia,contraentrega',
            'estado' => 'sometimes|string|in:pendiente,confirmado,en_proceso,listo_retiro,entregado,completado,cancelado',
            'estado_pago' => 'sometimes|string|in:pendiente,pagado,fallido',
            'notas' => 'nullable|string|max:1000',
            'notas_cancelacion' => 'nullable|string'
        ]);

        // Mapear notas a observaciones para mantener compatibilidad
        if (isset($validated['notas'])) {
            $validated['observaciones'] = $validated['notas'];
            unset($validated['notas']);
        }

        $order->update($validated);

        // Si se marca como pagado y es presencial, aplicar al inventario
        if ($order->isPresencial() && 
            isset($validated['estado_pago']) &&
            $validated['estado_pago'] === 'pagado' && 
            $order->wasChanged('estado_pago')) {
            $this->applyToInventory($order);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Orden actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // Solo permitir eliminar órdenes pendientes
        if ($order->estado !== 'pendiente') {
            return redirect()->back()
                ->withErrors(['error' => 'No se puede eliminar una orden que ya ha sido procesada.']);
        }

        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Orden eliminada exitosamente.');
    }

    /**
     * Aplicar venta al inventario
     */
    private function applyToInventory(Order $order)
    {
        // Crear movimiento de salida automático
        $movement = InventoryMovement::create([
            'tipo' => 'salida',
            'fecha' => now(),
            'referencia' => "Venta #{$order->numero_orden}",
            'observaciones' => "Venta " . ($order->isPresencial() ? 'presencial' : 'online'),
            'estado' => 'pendiente',
            'usuario_id' => $order->vendedor_id ?? $order->usuario_id,
        ]);

        foreach ($order->items as $item) {
            $movement->details()->create([
                'producto_id' => $item->producto_id,
                'cantidad' => $item->cantidad, // Positivo - el método aplicar() se encarga de la lógica
                'precio_unitario' => $item->precio,
                'observaciones' => "Venta de {$item->cantidad} unidades",
            ]);
        }

        // Aplicar al inventario
        $movement->aplicar();

        // La asociación se mantiene a través del campo 'referencia'
        // que contiene el número de orden
    }
}
