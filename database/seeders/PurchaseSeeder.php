<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Inventory;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all();
        $products = Product::all();

        if ($suppliers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No hay proveedores o productos disponibles para crear compras.');
            return;
        }

        // Crear 15 compras de ejemplo
        for ($i = 1; $i <= 15; $i++) {
            $supplier = $suppliers->random();
            $fecha = Carbon::now()->subDays(rand(0, 90));
            
            $purchase = Purchase::create([
                'nro' => sprintf('COMP-%04d', $i),
                'fecha' => $fecha->format('Y-m-d'),
                'hora' => $fecha->format('H:i'),
                'supplier_id' => $supplier->id,
                'observaciones' => $this->getRandomObservation(),
                'monto_total' => 0, // Se calculará después
                'estado' => $this->getRandomEstado($i),
                'fecha_recepcion' => $this->getFechaRecepcion($i, $fecha),
            ]);

            // Agregar entre 1 y 5 productos por compra
            $numberOfProducts = rand(1, 5);
            $selectedProducts = $products->random($numberOfProducts);
            $montoTotal = 0;

            foreach ($selectedProducts as $product) {
                $cantidad = rand(5, 50);
                $precio = round(rand(100, 5000) / 10, 2); // Precio entre $10.00 y $500.00

                $purchaseDetail = PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'cantidad_recibida' => $this->getCantidadRecibida($purchase->estado, $cantidad),
                    'estado_item' => $this->getEstadoItem($purchase->estado, $cantidad),
                    'observaciones_recepcion' => $this->getObservacionRecepcion($purchase->estado),
                ]);

                $montoTotal += $cantidad * $precio;

                // Solo actualizar inventario si la compra está recibida
                if (in_array($purchase->estado, ['recibida', 'parcial'])) {
                    $cantidadParaInventario = PurchaseDetail::find($purchaseDetail->id)->cantidad_recibida ?? 0;
                    
                    if ($cantidadParaInventario > 0) {
                        $inventory = Inventory::where('producto_id', $product->id)->first();
                        if ($inventory) {
                            $inventory->sumarStock($cantidadParaInventario);
                        }
                    }
                } else {
                    // Para compras pendientes, no actualizar inventario
                    $this->command->info("Compra #{$purchase->nro} creada como pendiente - inventario no actualizado");
                }
            }

            // Actualizar el monto total de la compra
            $purchase->update(['monto_total' => $montoTotal]);
        }

        $this->command->info('Se han creado 15 compras de ejemplo con sus detalles.');
    }

    private function getRandomObservation(): ?string
    {
        $observations = [
            'Compra urgente por falta de stock',
            'Productos para temporada alta',
            'Reposición de inventario regular',
            'Compra con descuento especial del proveedor',
            'Productos nuevos para catálogo',
            'Restock de productos populares',
            'Compra aprovechando promoción',
            'Pedido especial para cliente mayorista',
            null, // Sin observaciones
            null,
        ];

        return $observations[array_rand($observations)];
    }

    private function getRandomEstado(int $index): string
    {
        // Distribuir estados de manera realista
        return match(true) {
            $index <= 3 => 'pendiente',     // 20% pendientes
            $index <= 5 => 'parcial',       // 13% parciales
            $index == 6 => 'cancelada',     // 7% canceladas
            default => 'recibida'           // 60% recibidas
        };
    }

    private function getFechaRecepcion(int $index, Carbon $fechaCompra): ?Carbon
    {
        // Solo compras recibidas/parciales tienen fecha de recepción
        if (in_array($this->getRandomEstado($index), ['recibida', 'parcial'])) {
            return $fechaCompra->copy()->addDays(rand(1, 7)); // 1-7 días después
        }
        return null;
    }

    private function getCantidadRecibida(string $estado, int $cantidadPedida): ?int
    {
        return match($estado) {
            'pendiente', 'cancelada' => null,
            'recibida' => $cantidadPedida,
            'parcial' => max(1, intval($cantidadPedida * rand(60, 90) / 100)), // 60-90% de lo pedido
            default => null
        };
    }

    private function getEstadoItem(string $estadoCompra, int $cantidadPedida): string
    {
        return match($estadoCompra) {
            'pendiente' => 'pendiente',
            'cancelada' => 'faltante',
            'recibida' => 'completo',
            'parcial' => rand(0, 100) > 20 ? 'parcial' : 'faltante', // 80% parcial, 20% faltante
            default => 'pendiente'
        };
    }

    private function getObservacionRecepcion(string $estado): ?string
    {
        $observaciones = [
            'pendiente' => null,
            'cancelada' => 'Compra cancelada por el proveedor',
            'recibida' => null,
            'parcial' => [
                'Faltaron algunas unidades',
                'Proveedor envió en cantidad menor',
                'Productos dañados rechazados',
                'Entrega parcial por falta de stock',
                null
            ]
        ];

        if ($estado === 'parcial') {
            return $observaciones['parcial'][array_rand($observaciones['parcial'])];
        }

        return $observaciones[$estado] ?? null;
    }
}