<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\Category;
use App\Models\Measurement;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener categorías, medidas y proveedores existentes
        $categories = Category::all();
        $measurements = Measurement::all();
        $suppliers = Supplier::all();

        if ($categories->isEmpty() || $measurements->isEmpty() || $suppliers->isEmpty()) {
            $this->command->error('Debe ejecutar primero CategorySeeder, MeasurementSeeder y SupplierSeeder');
            return;
        }

        $products = [
            [
                'nombre' => 'Cemento Portland',
                'descripcion' => 'Cemento Portland tipo I, bolsa de 50 kg, ideal para construcción general',
                'precio' => 45.00,
                'category_name' => 'Material de Construcción',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Ferreterías del Norte S.A.',
                'stock' => 120,
                'min_stock' => 20,
                'price_sale' => 48.00
            ],
            [
                'nombre' => 'Varilla de Hierro 12mm',
                'descripcion' => 'Varilla de acero corrugado de 12mm x 12m para estructura',
                'precio' => 85.00,
                'category_name' => 'Material de Construcción',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Herramientas Industriales Ltda.',
                'stock' => 85,
                'min_stock' => 15,
                'price_sale' => 92.00
            ],
            [
                'nombre' => 'Ladrillo Gambote',
                'descripcion' => 'Ladrillo gambote de arcilla cocida 6H, medidas estándar',
                'precio' => 0.85,
                'category_name' => 'Material de Construcción',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Construcciones Paz S.R.L.',
                'stock' => 2500,
                'min_stock' => 500,
                'price_sale' => 0.95
            ],
            [
                'nombre' => 'Arena Fina',
                'descripcion' => 'Arena fina para mezcla y acabados, tamizada',
                'precio' => 180.00,
                'category_name' => 'Material de Construcción',
                'measurement_name' => 'Metro cuadrado',
                'supplier_name' => 'Construcciones Paz S.R.L.',
                'stock' => 15,
                'min_stock' => 3,
                'price_sale' => 200.00
            ],
            [
                'nombre' => 'Tubo PVC 4"',
                'descripcion' => 'Tubo PVC sanitario de 4 pulgadas x 6 metros',
                'precio' => 65.00,
                'category_name' => 'Fontanería',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Ferreterías del Norte S.A.',
                'stock' => 45,
                'min_stock' => 10,
                'price_sale' => 75.00
            ],
            [
                'nombre' => 'Cable THW 12 AWG',
                'descripcion' => 'Cable eléctrico THW calibre 12 AWG, color rojo',
                'precio' => 12.50,
                'category_name' => 'Electricidad',
                'measurement_name' => 'Metro',
                'supplier_name' => 'Eléctricos Andinos',
                'stock' => 250,
                'min_stock' => 50,
                'price_sale' => 15.00
            ],
            [
                'nombre' => 'Pintura Látex Blanco',
                'descripcion' => 'Pintura látex para interiores, color blanco, galón',
                'precio' => 85.00,
                'category_name' => 'Pintura',
                'measurement_name' => 'Litro',
                'supplier_name' => 'Pinturas y Acabados Bolivia',
                'stock' => 35,
                'min_stock' => 8,
                'price_sale' => 95.00
            ],
            [
                'nombre' => 'Martillo de Goma',
                'descripcion' => 'Martillo de goma para albañilería, mango de madera',
                'precio' => 45.00,
                'category_name' => 'Herramientas Manuales',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Herramientas Industriales Ltda.',
                'stock' => 18,
                'min_stock' => 5,
                'price_sale' => 55.00
            ],
            [
                'nombre' => 'Taladro 1/2"',
                'descripcion' => 'Taladro percutor de 1/2 pulgada, 650W, velocidad variable',
                'precio' => 320.00,
                'category_name' => 'Herramientas Eléctricas',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Herramientas Industriales Ltda.',
                'stock' => 8,
                'min_stock' => 2,
                'price_sale' => 380.00
            ],
            [
                'nombre' => 'Cerámica 30x30cm',
                'descripcion' => 'Cerámica para piso 30x30cm, color beige brillante',
                'precio' => 35.00,
                'category_name' => 'Material de Construcción',
                'measurement_name' => 'Metro cuadrado',
                'supplier_name' => 'Pinturas y Acabados Bolivia',
                'stock' => 65,
                'min_stock' => 15,
                'price_sale' => 42.00
            ],
            [
                'nombre' => 'Soldadura 6011',
                'descripcion' => 'Electrodo de soldadura E6011, 3.2mm x 350mm',
                'precio' => 1.20,
                'category_name' => 'Ferretería General',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Eléctricos Andinos',
                'stock' => 180,
                'min_stock' => 30,
                'price_sale' => 1.50
            ]
        ];

        // Crear inventario principal
        $inventory = Inventory::create([
            'fecha' => Carbon::now()->toDateString()
        ]);

        foreach ($products as $productData) {
            // Buscar IDs de categoría, medida y proveedor
            $category = $categories->where('nombre', $productData['category_name'])->first();
            $measurement = $measurements->where('nombre', $productData['measurement_name'])->first();
            $supplier = $suppliers->where('nombre_empresa', $productData['supplier_name'])->first();

            if (!$category) {
                $this->command->warn("Categoría no encontrada: {$productData['category_name']}");
                $this->command->info("Categorías disponibles: " . $categories->pluck('nombre')->join(', '));
                continue;
            }
            if (!$measurement) {
                $this->command->warn("Medida no encontrada: {$productData['measurement_name']}");
                $this->command->info("Medidas disponibles: " . $measurements->pluck('nombre')->join(', '));
                continue;
            }
            if (!$supplier) {
                $this->command->warn("Proveedor no encontrado: {$productData['supplier_name']}");
                $this->command->info("Proveedores disponibles: " . $suppliers->pluck('nombre_empresa')->join(', '));
                continue;
            }

            // Crear producto
            $product = Product::create([
                'nombre' => $productData['nombre'],
                'descripcion' => $productData['descripcion'],
                'precio' => $productData['precio'],
                'category_id' => $category->id,
                'measurement_id' => $measurement->id,
                'supplier_id' => $supplier->id,
            ]);

            // Crear detalle de inventario
            InventoryDetail::create([
                'inventory_id' => $inventory->id,
                'product_id' => $product->id,
                'cantidad' => $productData['stock'],
                'cantidad_minima' => $productData['min_stock'],
                'precio_venta' => $productData['price_sale'],
            ]);
        }

        $this->command->info('Productos e inventario creados exitosamente.');
    }
}