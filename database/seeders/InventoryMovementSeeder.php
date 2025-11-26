<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryMovement;
use App\Models\InventoryMovementDetail;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class InventoryMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $products = Product::take(5)->get();
        
        if ($user && $products->count() > 0) {
            // Crear algunas entradas
            for ($i = 0; $i < 3; $i++) {
                $movement = InventoryMovement::create([
                    'tipo' => 'entrada',
                    'fecha' => Carbon::now()->subDays(rand(1, 30)),
                    'referencia' => 'ENT-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'observaciones' => 'Entrada de mercancía del proveedor ' . ($i + 1),
                    'estado' => 'aplicado',
                    'usuario_id' => $user->id,
                ]);

                // Agregar productos a cada movimiento
                $selectedProducts = $products->random(rand(1, 3));
                foreach ($selectedProducts as $product) {
                    InventoryMovementDetail::create([
                        'movimiento_id' => $movement->id,
                        'producto_id' => $product->id,
                        'cantidad' => rand(5, 50),
                        'precio_unitario' => $product->precio_compra ?? rand(1000, 5000),
                        'observaciones' => 'Stock inicial',
                    ]);
                }
            }

            // Crear algunas salidas
            for ($i = 0; $i < 2; $i++) {
                $movement = InventoryMovement::create([
                    'tipo' => 'salida',
                    'fecha' => Carbon::now()->subDays(rand(1, 15)),
                    'referencia' => 'SAL-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'observaciones' => 'Venta a cliente',
                    'estado' => 'aplicado',
                    'usuario_id' => $user->id,
                ]);

                // Agregar productos
                $selectedProducts = $products->random(rand(1, 2));
                foreach ($selectedProducts as $product) {
                    InventoryMovementDetail::create([
                        'movimiento_id' => $movement->id,
                        'producto_id' => $product->id,
                        'cantidad' => rand(1, 10),
                        'precio_unitario' => $product->precio_venta ?? rand(1500, 6000),
                        'observaciones' => 'Venta',
                    ]);
                }
            }

            // Crear un ajuste pendiente
            $movement = InventoryMovement::create([
                'tipo' => 'ajuste',
                'fecha' => Carbon::now()->subDays(1),
                'referencia' => 'AJU-0001',
                'observaciones' => 'Ajuste por inventario físico',
                'estado' => 'pendiente',
                'usuario_id' => $user->id,
            ]);

            InventoryMovementDetail::create([
                'movimiento_id' => $movement->id,
                'producto_id' => $products->first()->id,
                'cantidad' => 2,
                'precio_unitario' => $products->first()->precio_venta ?? 2000,
                'observaciones' => 'Diferencia encontrada en conteo físico',
            ]);

            $this->command->info('✅ Movimientos de inventario creados exitosamente');
        } else {
            $this->command->error('❌ No hay usuarios o productos disponibles');
        }
    }
}
