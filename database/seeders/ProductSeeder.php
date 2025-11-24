<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear algunos proveedores de muestra
        $suppliers = [
            ['name' => 'Ferretería Central', 'contact_name' => 'Juan Pérez', 'email' => 'juan@ferreteriacentral.com', 'phone' => '555-1234'],
            ['name' => 'Distribuidora Norte', 'contact_name' => 'María González', 'email' => 'maria@distrinorte.com', 'phone' => '555-5678'],
            ['name' => 'Herramientas del Sur', 'contact_name' => 'Carlos López', 'email' => 'carlos@herramientasur.com', 'phone' => '555-9012'],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        // Obtener categorías e IDs de proveedores
        $herramientasManuales = Category::where('name', 'Herramientas Manuales')->first();
        $herramientasElectricas = Category::where('name', 'Herramientas Eléctricas')->first();
        $construccion = Category::where('name', 'Material de Construcción')->first();
        $fontaneria = Category::where('name', 'Fontanería')->first();
        $electricidad = Category::where('name', 'Electricidad')->first();
        $ferreteria = Category::where('name', 'Ferretería General')->first();

        $supplier1 = Supplier::first();
        $supplier2 = Supplier::skip(1)->first();
        $supplier3 = Supplier::skip(2)->first();

        // Productos de muestra
        $products = [
            // Herramientas Manuales
            ['name' => 'Martillo de Garra 16oz', 'description' => 'Martillo con mango de fibra de vidrio', 'sku' => 'HM001', 'price' => 250.00, 'cost' => 150.00, 'stock' => 25, 'min_stock' => 5, 'category_id' => $herramientasManuales->id, 'supplier_id' => $supplier1->id],
            ['name' => 'Destornillador Phillips #2', 'description' => 'Destornillador con mango ergonómico', 'sku' => 'HM002', 'price' => 45.00, 'cost' => 25.00, 'stock' => 50, 'min_stock' => 10, 'category_id' => $herramientasManuales->id, 'supplier_id' => $supplier1->id],
            ['name' => 'Llave Inglesa 10"', 'description' => 'Llave inglesa ajustable cromada', 'sku' => 'HM003', 'price' => 180.00, 'cost' => 110.00, 'stock' => 3, 'min_stock' => 5, 'category_id' => $herramientasManuales->id, 'supplier_id' => $supplier2->id],
            
            // Herramientas Eléctricas
            ['name' => 'Taladro Inalámbrico 18V', 'description' => 'Taladro con batería de litio incluida', 'sku' => 'HE001', 'price' => 1250.00, 'cost' => 800.00, 'stock' => 8, 'min_stock' => 3, 'category_id' => $herramientasElectricas->id, 'supplier_id' => $supplier2->id],
            ['name' => 'Sierra Circular 7¼"', 'description' => 'Sierra circular 1400W con disco incluido', 'sku' => 'HE002', 'price' => 950.00, 'cost' => 650.00, 'stock' => 5, 'min_stock' => 2, 'category_id' => $herramientasElectricas->id, 'supplier_id' => $supplier3->id],
            
            // Material de Construcción
            ['name' => 'Cemento Gris 50kg', 'description' => 'Cemento Portland tipo I', 'sku' => 'MC001', 'price' => 180.00, 'cost' => 120.00, 'stock' => 100, 'min_stock' => 20, 'unit' => 'bulto', 'category_id' => $construccion->id, 'supplier_id' => $supplier1->id],
            ['name' => 'Varilla de Acero 3/8"', 'description' => 'Varilla corrugada 6 metros', 'sku' => 'MC002', 'price' => 85.00, 'cost' => 55.00, 'stock' => 200, 'min_stock' => 50, 'unit' => 'pieza', 'category_id' => $construccion->id, 'supplier_id' => $supplier2->id],
            
            // Fontanería
            ['name' => 'Tubo PVC 2" x 6m', 'description' => 'Tubo para desagüe PVC', 'sku' => 'FO001', 'price' => 95.00, 'cost' => 65.00, 'stock' => 2, 'min_stock' => 10, 'unit' => 'pieza', 'category_id' => $fontaneria->id, 'supplier_id' => $supplier3->id],
            ['name' => 'Llave de Paso 1/2"', 'description' => 'Llave esférica de bronce', 'sku' => 'FO002', 'price' => 125.00, 'cost' => 80.00, 'stock' => 15, 'min_stock' => 5, 'category_id' => $fontaneria->id, 'supplier_id' => $supplier1->id],
            
            // Electricidad
            ['name' => 'Cable THW 12 AWG', 'description' => 'Cable de cobre para instalaciones', 'sku' => 'EL001', 'price' => 25.00, 'cost' => 15.00, 'stock' => 500, 'min_stock' => 100, 'unit' => 'metro', 'category_id' => $electricidad->id, 'supplier_id' => $supplier2->id],
            ['name' => 'Interruptor Sencillo', 'description' => 'Interruptor 15A 127V color blanco', 'sku' => 'EL002', 'price' => 35.00, 'cost' => 20.00, 'stock' => 1, 'min_stock' => 10, 'category_id' => $electricidad->id, 'supplier_id' => $supplier3->id],
            
            // Ferretería General
            ['name' => 'Tornillos Autorroscantes 1"', 'description' => 'Caja con 100 tornillos galvanizados', 'sku' => 'FG001', 'price' => 45.00, 'cost' => 25.00, 'stock' => 30, 'min_stock' => 10, 'unit' => 'caja', 'category_id' => $ferreteria->id, 'supplier_id' => $supplier1->id],
            ['name' => 'Bisagras de Puerta 3"', 'description' => 'Par de bisagras de acero inoxidable', 'sku' => 'FG002', 'price' => 85.00, 'cost' => 50.00, 'stock' => 20, 'min_stock' => 5, 'unit' => 'par', 'category_id' => $ferreteria->id, 'supplier_id' => $supplier2->id],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
