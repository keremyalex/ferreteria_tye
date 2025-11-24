<?php

namespace Database\Seeders;

use App\Models\Measurement;
use Illuminate\Database\Seeder;

class MeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $measurements = [
            ['nombre' => 'Unidad', 'simbolo' => 'un'],
            ['nombre' => 'Kilogramo', 'simbolo' => 'kg'],
            ['nombre' => 'Gramo', 'simbolo' => 'g'],
            ['nombre' => 'Litro', 'simbolo' => 'L'],
            ['nombre' => 'Metro', 'simbolo' => 'm'],
            ['nombre' => 'Centímetro', 'simbolo' => 'cm'],
            ['nombre' => 'Milímetro', 'simbolo' => 'mm'],
            ['nombre' => 'Pulgada', 'simbolo' => 'in'],
            ['nombre' => 'Pie', 'simbolo' => 'ft'],
            ['nombre' => 'Caja', 'simbolo' => 'caja'],
            ['nombre' => 'Paquete', 'simbolo' => 'paq'],
            ['nombre' => 'Docena', 'simbolo' => 'doc'],
            ['nombre' => 'Metro cuadrado', 'simbolo' => 'm²'],
        ];

        foreach ($measurements as $measurement) {
            Measurement::create($measurement);
        }
    }
}
