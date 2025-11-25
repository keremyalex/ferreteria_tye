<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'cliente.direccion' => 'required|string',
            'cliente.ciudad' => 'required|string|max:100',
            'cliente.email' => 'required|email',
            'metodo_pago' => 'required|in:contraentrega,transferencia',
            'subtotal' => 'required|numeric|min:0',
            'envio' => 'required|numeric|min:0',
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

            // Crear la orden
            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT),
                'status' => 'pendiente',
                'subtotal' => $request->subtotal,
                'tax' => 0,
                'shipping' => $request->envio,
                'total' => $request->total,
                'shipping_address' => $request->cliente,
                'billing_address' => $request->cliente,
                'notes' => $request->notas,
                'payment_method' => $request->metodo_pago,
                'payment_status' => 'pendiente',
                'user_id' => auth()->id(),
            ]);

            // Crear los items de la orden y actualizar inventario
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                // Crear item de la orden
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['cantidad'],
                    'price' => $item['precio_unitario'],
                    'total' => $item['cantidad'] * $item['precio_unitario'],
                ]);

                // Actualizar inventario
                $inventory = $product->inventory;
                $inventory->decrement('cantidad_actual', $item['cantidad']);
            }

            return response()->json([
                'success' => true,
                'order' => $order,
                'message' => 'Pedido procesado exitosamente'
            ]);
        });
    }
}
