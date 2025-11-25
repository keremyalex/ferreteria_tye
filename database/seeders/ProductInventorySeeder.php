<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\Measurement;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;

class ProductInventorySeeder extends Seeder
{
    /**
     * Genera una imagen usando diferentes métodos
     */
    private function generateProductImage($productName, $index)
    {
        // Primero intentar generar URLs de imágenes reales de ejemplo
        $productImages = $this->getProductImageUrls();
        
        // Si tenemos URLs predefinidas, usarlas
        if (isset($productImages[$index])) {
            return $productImages[$index];
        }
        
        // Si no, usar diferentes servicios de placeholder
        return $this->generatePlaceholderImage($productName, $index);
    }
    
    /**
     * URLs de imágenes reales para productos de ferretería
     */
    private function getProductImageUrls()
    {
        return [
            'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=400&h=400&fit=crop', // Cemento
            'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&h=400&fit=crop', // Hierro
            'https://images.unsplash.com/photo-1558618137-1ae84435df97?w=400&h=400&fit=crop', // Ladrillo
            'https://images.unsplash.com/photo-1540979388789-6cee28a1cdc9?w=400&h=400&fit=crop', // Arena
            'https://images.unsplash.com/photo-1540979388789-6cee28a1cdc9?w=400&h=400&fit=crop', // Grava
            'https://images.unsplash.com/photo-1572981779307-38b8cabb2407?w=400&h=400&fit=crop', // Martillo
            'https://images.unsplash.com/photo-1581092786450-7ef4081c6c6d?w=400&h=400&fit=crop', // Destornillador
            'https://images.unsplash.com/photo-1581092786450-7ef4081c6c6d?w=400&h=400&fit=crop', // Alicate
            'https://images.unsplash.com/photo-1581092786450-7ef4081c6c6d?w=400&h=400&fit=crop', // Llave
            'https://images.unsplash.com/photo-1558618148-fbd26c9ac26f?w=400&h=400&fit=crop', // Candado
            'https://images.unsplash.com/photo-1558618148-fbd26c9ac26f?w=400&h=400&fit=crop', // Chapa
            'https://images.unsplash.com/photo-1558618148-fbd26c9ac26f?w=400&h=400&fit=crop', // Bisagra
            'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=400&h=400&fit=crop', // Cable
            'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=400&h=400&fit=crop', // Interruptor
            'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=400&h=400&fit=crop', // Tomacorriente
        ];
    }
    
    /**
     * Genera placeholder con diferentes servicios
     */
    private function generatePlaceholderImage($productName, $index)
    {
        $services = [
            'via.placeholder.com',
            'picsum.photos', 
            'dummyimage.com'
        ];
        
        $service = $services[$index % count($services)];
        $colors = ['3498db', '2ecc71', 'e74c3c', 'f39c12', '9b59b6', '34495e', 'f1c40f', '16a085'];
        $color = $colors[$index % count($colors)];
        
        $text = substr($productName, 0, 15);
        $encodedText = urlencode($text);
        
        switch ($service) {
            case 'picsum.photos':
                return "https://picsum.photos/400/400?random={$index}";
                
            case 'dummyimage.com':
                return "https://dummyimage.com/400x400/{$color}/ffffff&text={$encodedText}";
                
            default:
                return "https://via.placeholder.com/400x400/{$color}/ffffff?text={$encodedText}";
        }
    }

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
                'precio_venta' => 48.00,
                'category_name' => 'Material de Construcción',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Ferreterías del Norte S.A.',
                'stock' => 120,
                'min_stock' => 20,
                'max_stock' => 240,
                'precio_venta_especifico' => null
            ],
            [
                'nombre' => 'Varilla de Hierro 12mm',
                'descripcion' => 'Varilla de acero corrugado de 12mm x 12m para estructura',
                'precio_venta' => 92.00,
                'category_name' => 'Material de Construcción',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Herramientas Industriales Ltda.',
                'stock' => 85,
                'min_stock' => 15,
                'max_stock' => 170,
                'precio_venta_especifico' => null
            ],
            [
                'nombre' => 'Ladrillo Gambote',
                'descripcion' => 'Ladrillo gambote de arcilla cocida 6H, medidas estándar',
                'precio_venta' => 0.95,
                'category_name' => 'Material de Construcción',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Construcciones Paz S.R.L.',
                'stock' => 2500,
                'min_stock' => 500,
                'max_stock' => 5000,
                'precio_venta_especifico' => null
            ],
            [
                'nombre' => 'Arena Fina',
                'descripcion' => 'Arena fina para mezcla y acabados, tamizada',
                'precio_venta' => 200.00,
                'category_name' => 'Material de Construcción',
                'measurement_name' => 'Metro cuadrado',
                'supplier_name' => 'Construcciones Paz S.R.L.',
                'stock' => 15,
                'min_stock' => 3,
                'max_stock' => 30,
                'precio_venta_especifico' => null
            ],
            [
                'nombre' => 'Grava',
                'descripcion' => 'Grava triturada para hormigón, tamaño 20-40mm',
                'precio_venta' => 180.00,
                'category_name' => 'Material de Construcción',
                'measurement_name' => 'Metro cuadrado',
                'supplier_name' => 'Construcciones Paz S.R.L.',
                'stock' => 18,
                'min_stock' => 4,
                'max_stock' => 36,
                'precio_venta_especifico' => null
            ],
            [
                'nombre' => 'Martillo de Garra 16oz',
                'descripcion' => 'Martillo carpintero con garra, mango de fibra de vidrio 16 oz',
                'precio_venta' => 85.00,
                'category_name' => 'Herramientas Manuales',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Herramientas Industriales Ltda.',
                'stock' => 45,
                'min_stock' => 10,
                'max_stock' => 90,
                'precio_venta_especifico' => null
            ],
            [
                'nombre' => 'Destornillador Phillips #2',
                'descripcion' => 'Destornillador punta Phillips #2, mango ergonómico',
                'precio_venta' => 25.00,
                'category_name' => 'Herramientas Manuales',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Herramientas Industriales Ltda.',
                'stock' => 75,
                'min_stock' => 15,
                'max_stock' => 150,
                'precio_venta_especifico' => null
            ],
            [
                'nombre' => 'Alicate Universal 8"',
                'descripcion' => 'Alicate universal 8 pulgadas, acero al carbono',
                'precio_venta' => 65.00,
                'category_name' => 'Herramientas Manuales',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Herramientas Industriales Ltda.',
                'stock' => 35,
                'min_stock' => 8,
                'max_stock' => 70,
                'precio_venta_especifico' => null
            ],
            [
                'nombre' => 'Llave Inglesa 10"',
                'descripcion' => 'Llave inglesa ajustable 10 pulgadas, cromada',
                'precio_venta' => 75.00,
                'category_name' => 'Herramientas Manuales',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Herramientas Industriales Ltda.',
                'stock' => 28,
                'min_stock' => 6,
                'max_stock' => 56,
                'precio_venta_especifico' => null
            ],
            [
                'nombre' => 'Candado de 50mm',
                'descripcion' => 'Candado de seguridad 50mm, cuerpo de latón macizo',
                'precio_venta' => 45.00,
                'category_name' => 'Cerrajería',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Seguridad Total EIRL',
                'stock' => 60,
                'min_stock' => 12,
                'max_stock' => 120,
                'precio_venta_especifico' => null
            ],
            [
                'nombre' => 'Chapa de Sobreponer',
                'descripcion' => 'Chapa de sobreponer para puerta, incluye 3 llaves',
                'precio_venta' => 120.00,
                'category_name' => 'Cerrajería',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Seguridad Total EIRL',
                'stock' => 25,
                'min_stock' => 5,
                'max_stock' => 50,
                'precio_venta_especifico' => null
            ],
            [
                'nombre' => 'Bisagra 4" Acero',
                'descripcion' => 'Bisagra para puerta 4 pulgadas, acero galvanizado',
                'precio_venta' => 18.00,
                'category_name' => 'Cerrajería',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Seguridad Total EIRL',
                'stock' => 150,
                'min_stock' => 30,
                'max_stock' => 300,
                'precio_venta_especifico' => null
            ],
            [
                'nombre' => 'Cable THW 12 AWG',
                'descripcion' => 'Cable eléctrico THW 12 AWG, cobre sólido',
                'precio_venta' => 8.50,
                'category_name' => 'Material Eléctrico',
                'measurement_name' => 'Metro',
                'supplier_name' => 'Eléctricos Modernos S.A.',
                'stock' => 500,
                'min_stock' => 100,
                'max_stock' => 1000,
                'precio_venta_especifico' => null
            ],
            [
                'nombre' => 'Interruptor Simple',
                'descripcion' => 'Interruptor simple 10A, color blanco',
                'precio_venta' => 15.00,
                'category_name' => 'Material Eléctrico',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Eléctricos Modernos S.A.',
                'stock' => 80,
                'min_stock' => 20,
                'max_stock' => 160,
                'precio_venta_especifico' => null
            ],
            [
                'nombre' => 'Tomacorriente Doble',
                'descripcion' => 'Tomacorriente doble con tierra, 15A, color blanco',
                'precio_venta' => 22.00,
                'category_name' => 'Material Eléctrico',
                'measurement_name' => 'Unidad',
                'supplier_name' => 'Eléctricos Modernos S.A.',
                'stock' => 65,
                'min_stock' => 15,
                'max_stock' => 130,
                'precio_venta_especifico' => null
            ]
        ];

        $this->command->info('Creando productos e inventario con imágenes...');

        foreach ($products as $index => $productData) {
            // Buscar relaciones
            $category = $categories->firstWhere('nombre', $productData['category_name']);
            $measurement = $measurements->firstWhere('nombre', $productData['measurement_name']);
            $supplier = $suppliers->firstWhere('nombre_empresa', $productData['supplier_name']);

            // Validar que existan las relaciones
            if (!$category) {
                $this->command->warn("Categoría no encontrada: {$productData['category_name']}");
                continue;
            }
            if (!$measurement) {
                $this->command->warn("Medida no encontrada: {$productData['measurement_name']}");
                continue;
            }
            if (!$supplier) {
                $this->command->warn("Proveedor no encontrado: {$productData['supplier_name']}");
                continue;
            }

            // Generar imagen para el producto
            $imagePath = $this->generateProductImage($productData['nombre'], $index);

            // Crear producto
            $product = Product::create([
                'nombre' => $productData['nombre'],
                'descripcion' => $productData['descripcion'],
                'imagen' => $imagePath,
                'precio_venta' => $productData['precio_venta'],
                'category_id' => $category->id,
                'measurement_id' => $measurement->id,
                'supplier_id' => $supplier->id,
            ]);

            // Crear registro de inventario
            Inventory::create([
                'producto_id' => $product->id,
                'cantidad_actual' => $productData['stock'],
                'cantidad_minima' => $productData['min_stock'],
                'cantidad_maxima' => $productData['max_stock'],
                'precio_venta' => $productData['precio_venta_especifico'],
            ]);

            $this->command->info("Producto creado: {$productData['nombre']}" . ($imagePath ? " con imagen" : ""));
        }

        $this->command->info('Productos e inventario creados exitosamente.');
    }
}