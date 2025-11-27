<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener clientes y usuarios
        $clientes = Client::all();
        $vendedores = User::whereHas('roles', function($query) {
            $query->where('name', 'ventas');
        })->get();

        if ($clientes->isEmpty()) {
            $this->command->warn('No hay clientes. Ejecuta primero ClientSeeder');
            return;
        }

        if ($vendedores->isEmpty()) {
            $this->command->warn('No hay vendedores. Se crearán órdenes sin vendedor asignado');
        }

        // Obtener productos disponibles en inventario
        $productosConStock = Inventory::with('product')
            ->where('cantidad_actual', '>', 0)
            ->get();

        if ($productosConStock->isEmpty()) {
            $this->command->warn('No hay productos con stock. Ejecuta primero ProductInventorySeeder');
            return;
        }

        // Crear órdenes de los últimos 30 días
        $fechasOrden = [
            Carbon::now()->subDays(25),
            Carbon::now()->subDays(20),
            Carbon::now()->subDays(18),
            Carbon::now()->subDays(15),
            Carbon::now()->subDays(12),
            Carbon::now()->subDays(10),
            Carbon::now()->subDays(8),
            Carbon::now()->subDays(6),
            Carbon::now()->subDays(4),
            Carbon::now()->subDays(2),
            Carbon::now()->subDays(1),
            Carbon::now(),
        ];

        $estados = ['pendiente', 'confirmado', 'en_proceso', 'completado', 'cancelado'];
        $tiposOrden = ['online', 'presencial'];
        $metodosPago = ['contraentrega', 'transferencia', 'tarjeta', 'efectivo', 'qr'];
        $estadosPago = ['pendiente', 'pagado', 'fallido'];

        // Obtener el último número de orden existente
        $ultimaOrden = Order::orderBy('numero_orden', 'desc')->first();
        $numeroOrden = 1;
        
        if ($ultimaOrden && preg_match('/ORD-\d{4}-(\d+)/', $ultimaOrden->numero_orden, $matches)) {
            $numeroOrden = intval($matches[1]) + 1;
        }

        foreach ($fechasOrden as $fecha) {
            // Crear 1-3 órdenes por día
            $numOrdenes = rand(1, 3);
            
            for ($i = 0; $i < $numOrdenes; $i++) {
                $cliente = $clientes->random();
                $vendedor = $vendedores->isNotEmpty() ? $vendedores->random() : null;
                $estado = $estados[array_rand($estados)];
                $tipo = $tiposOrden[array_rand($tiposOrden)];
                
                // Las órdenes más antiguas tienen más probabilidad de estar completadas
                $diasAtras = Carbon::now()->diffInDays($fecha);
                if ($diasAtras > 10) {
                    $estado = rand(1, 10) <= 8 ? 'completado' : $estado;
                } elseif ($diasAtras > 5) {
                    $estado = rand(1, 10) <= 6 ? 'completado' : $estado;
                }

                $metodoPago = $metodosPago[array_rand($metodosPago)];
                $estadoPago = $estado === 'completado' ? 'pagado' : 
                            ($estado === 'cancelado' ? 'fallido' : $estadosPago[array_rand($estadosPago)]);

                $orden = Order::create([
                    'numero_orden' => 'ORD-' . $fecha->format('Y') . '-' . str_pad($numeroOrden, 6, '0', STR_PAD_LEFT),
                    'tipo' => $tipo,
                    'estado' => $estado,
                    'subtotal' => 0, // Se calculará después
                    'total' => 0,
                    'metodo_pago' => $metodoPago,
                    'estado_pago' => $estadoPago,
                    'cliente_id' => $cliente->id,
                    'vendedor_id' => $vendedor?->id,
                    'direccion_facturacion' => json_encode([
                        'direccion' => 'Av. Principal #' . rand(100, 999) . ', La Paz',
                        'telefono' => '7' . rand(1000000, 9999999),
                    ]),
                    'observaciones' => rand(1, 10) <= 3 ? 'Entrega urgente' : null,
                    'created_at' => $fecha,
                    'updated_at' => $fecha->copy()->addHours(rand(1, 6)),
                ]);

                $numeroOrden++;

                // Agregar items a la orden (1-4 productos diferentes)
                $numItems = rand(1, 4);
                $productosSeleccionados = $productosConStock->random($numItems);
                $subtotal = 0;

                foreach ($productosSeleccionados as $inventario) {
                    $producto = $inventario->product;
                    $cantidad = rand(1, min(5, $inventario->cantidad_actual));
                    $precioUnitario = $producto->precio_venta;
                    $subtotalItem = $cantidad * $precioUnitario;

                    OrderItem::create([
                        'orden_id' => $orden->id,
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'precio' => $precioUnitario,
                        'total' => $subtotalItem,
                    ]);

                    $subtotal += $subtotalItem;

                    // Si la orden está completada, reducir el inventario
                    if ($estado === 'completado') {
                        $inventario->cantidad_actual -= $cantidad;
                        $inventario->save();
                    }
                }

                // Calcular total (sin impuestos separados como en la estructura original)
                $total = $subtotal;

                $orden->update([
                    'subtotal' => $subtotal,
                    'total' => $total,
                ]);
            }
        }

        // Crear algunas órdenes del mes pasado para comparación
        $mesAnterior = Carbon::now()->subMonth();
        for ($i = 0; $i < 8; $i++) {
            $fechaAnterior = $mesAnterior->copy()->addDays(rand(0, 28));
            $cliente = $clientes->random();
            $vendedor = $vendedores->isNotEmpty() ? $vendedores->random() : null;
            
            $orden = Order::create([
                'numero_orden' => 'ORD-' . $fechaAnterior->format('Y') . '-' . str_pad($numeroOrden, 6, '0', STR_PAD_LEFT),
                'tipo' => 'presencial',
                'estado' => 'completado',
                'subtotal' => 0,
                'total' => 0,
                'metodo_pago' => ['efectivo', 'tarjeta'][array_rand(['efectivo', 'tarjeta'])],
                'estado_pago' => 'pagado',
                'cliente_id' => $cliente->id,
                'vendedor_id' => $vendedor?->id,
                'direccion_facturacion' => json_encode([
                    'direccion' => 'Calle Secundaria #' . rand(100, 999),
                    'telefono' => '7' . rand(1000000, 9999999),
                ]),
                'created_at' => $fechaAnterior,
                'updated_at' => $fechaAnterior->copy()->addHours(2),
            ]);

            $numeroOrden++;

            $productosSeleccionados = $productosConStock->random(rand(1, 3));
            $subtotal = 0;

            foreach ($productosSeleccionados as $inventario) {
                $producto = $inventario->product;
                $cantidad = rand(1, 3);
                $precioUnitario = $producto->precio_venta;
                $subtotalItem = $cantidad * $precioUnitario;

                OrderItem::create([
                    'orden_id' => $orden->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio' => $precioUnitario,
                    'total' => $subtotalItem,
                ]);

                $subtotal += $subtotalItem;
            }

            $total = $subtotal;

            $orden->update([
                'subtotal' => $subtotal,
                'total' => $total,
            ]);
        }

        $totalOrdenes = Order::count();
        $ordenesCompletadas = Order::where('estado', 'completado')->count();
        $ingresosTotales = Order::where('estado', 'completado')->sum('total');

        $this->command->info("✅ Órdenes creadas exitosamente");
        $this->command->info("📊 Total de órdenes: {$totalOrdenes}");
        $this->command->info("✅ Órdenes completadas: {$ordenesCompletadas}");
        $this->command->info("💰 Ingresos totales: Bs " . number_format($ingresosTotales, 2));
        $this->command->info("📈 Órdenes distribuidas en los últimos 30 días");
    }
}