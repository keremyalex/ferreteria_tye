<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientOrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:cliente');
    }

    /**
     * Display client's orders
     */
    public function index()
    {
        $orders = Order::where('usuario_id', auth()->id())
            ->where('tipo', 'online')
            ->with(['items.product', 'client'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(function ($order) {
                return [
                    'id' => $order->id,
                    'numero_orden' => $order->numero_orden,
                    'estado' => $order->estado,
                    'estado_pago' => $order->estado_pago,
                    'metodo_pago' => $order->metodo_pago,
                    'subtotal' => $order->subtotal,
                    'total' => $order->total,
                    'direccion_facturacion' => $order->direccion_facturacion,
                    'observaciones' => $order->observaciones,
                    'created_at' => $order->created_at->format('d/m/Y H:i'),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'producto_nombre' => $item->product->nombre,
                            'cantidad' => $item->cantidad,
                            'precio' => $item->precio,
                            'subtotal' => $item->subtotal,
                            'imagen_url' => $item->product->imagen_url,
                        ];
                    }),
                ];
            });

        return Inertia::render('Client/MisPedidos', [
            'orders' => $orders
        ]);
    }

    /**
     * Show specific order details
     */
    public function show(Order $order)
    {
        // Verificar que la orden pertenece al usuario
        if ($order->usuario_id !== auth()->id() || $order->tipo !== 'online') {
            abort(403, 'No autorizado para ver esta orden');
        }

        $order->load(['items.product', 'client']);

        return Inertia::render('Client/VerPedido', [
            'order' => [
                'id' => $order->id,
                'numero_orden' => $order->numero_orden,
                'estado' => $order->estado,
                'estado_pago' => $order->estado_pago,
                'metodo_pago' => $order->metodo_pago,
                'subtotal' => $order->subtotal,
                'total' => $order->total,
                'direccion_facturacion' => $order->direccion_facturacion,
                'observaciones' => $order->observaciones,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'updated_at' => $order->updated_at->format('d/m/Y H:i'),
                'items' => $order->items->map(function ($item) {
                    return [
                        'producto_nombre' => $item->product->nombre,
                        'cantidad' => $item->cantidad,
                        'precio' => $item->precio,
                        'subtotal' => $item->subtotal,
                        'imagen_url' => $item->product->imagen_url,
                    ];
                }),
            ]
        ]);
    }
}
