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
            ]);

            // Agregar entre 1 y 5 productos por compra
            $numberOfProducts = rand(1, 5);
            $selectedProducts = $products->random($numberOfProducts);
            $montoTotal = 0;

            foreach ($selectedProducts as $product) {
                $cantidad = rand(5, 50);
                $precio = round(rand(100, 5000) / 10, 2); // Precio entre $10.00 y $500.00

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                ]);

                $montoTotal += $cantidad * $precio;

                // Actualizar inventario
                $inventory = Inventory::where('producto_id', $product->id)
                                     ->first();

                if ($inventory) {
                    $inventory->sumarStock($cantidad);
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
}