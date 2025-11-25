<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Herramientas Manuales',
            'Herramientas Eléctricas', 
            'Material de Construcción',
            'Fontanería',
            'Material Eléctrico',
            'Cerrajería',
            'Pintura',
            'Ferretería General',
            'Jardinería',
            'Seguridad',
            'Adhesivos y Selladores',
        ];

        foreach ($categories as $category) {
            Category::create([
                'nombre' => $category,
            ]);
        }
    }
}
