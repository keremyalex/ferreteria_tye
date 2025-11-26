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

            // Buscar o crear cliente temporal para la orden online
            $cliente = $this->findOrCreateOnlineClient($request->cliente);

            // Crear la orden usando el esquema unificado
            $order = Order::create([
                'tipo' => 'online',
                'estado' => 'pendiente',
                'subtotal' => $request->subtotal,
                'total' => $request->total,
                'direccion_facturacion' => [
                    'nombre' => $request->cliente['nombre'],
                    'email' => $request->cliente['email'],
                    'telefono' => $request->cliente['telefono'],
                    'direccion' => $request->cliente['direccion'],
                    'ciudad' => $request->cliente['ciudad'],
                    'codigo_postal' => $request->cliente['codigo_postal'] ?? null,
                    'envio' => $request->envio,
                ],
                'observaciones' => $request->notas,
                'metodo_pago' => $request->metodo_pago,
                'estado_pago' => 'pendiente',
                'usuario_id' => auth()->id(),
                'cliente_id' => $cliente->id,
                'vendedor_id' => null, // Las ventas online no tienen vendedor asignado
            ]);

            // Crear los items de la orden usando el esquema unificado
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                OrderItem::create([
                    'orden_id' => $order->id, // Usar 'orden_id' en lugar de 'order_id'
                    'producto_id' => $item['product_id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio_unitario'],
                    'subtotal' => $item['cantidad'] * $item['precio_unitario'],
                ]);
            }

            // NO creamos movimiento de inventario aquí para órdenes online pendientes
            // Se creará cuando la orden sea marcada como pagada

            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'numero_orden' => $order->numero_orden,
                    'tipo' => $order->tipo,
                    'estado' => $order->estado,
                    'total' => $order->total,
                ],
                'message' => 'Pedido procesado exitosamente'
            ]);
        });
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
