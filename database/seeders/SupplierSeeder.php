<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'nit' => '12345678-9',
                'nombre_empresa' => 'Ferreterías del Norte S.A.',
                'nombre_persona' => 'Juan Carlos Pérez',
                'direccion' => 'Av. Principal 123, Zona Norte',
                'telefono' => '2-2345678',
                'correo' => 'ventas@ferreteriasdelnorete.com'
            ],
            [
                'nit' => '87654321-7',
                'nombre_empresa' => 'Herramientas Industriales Ltda.',
                'nombre_persona' => 'María Elena López',
                'direccion' => 'Calle Comercio 456, Zona Industrial',
                'telefono' => '2-7654321',
                'correo' => 'contacto@herramientasind.com'
            ],
            [
                'nit' => '11111111-1',
                'nombre_empresa' => 'Pinturas y Acabados Bolivia',
                'nombre_persona' => 'Roberto Silva',
                'direccion' => 'Plaza San Martín 789',
                'telefono' => '2-1111111',
                'correo' => 'info@pinturasbolivia.com'
            ],
            [
                'nit' => '22222222-2',
                'nombre_empresa' => 'Construcciones Paz S.R.L.',
                'nombre_persona' => 'Ana Gutiérrez',
                'direccion' => 'Av. 6 de Agosto 1010',
                'telefono' => '2-2222222',
                'correo' => 'ventas@construccionespaz.com'
            ],
            [
                'nit' => '33333333-3',
                'nombre_empresa' => 'Eléctricos Modernos S.A.',
                'nombre_persona' => 'Carlos Mamani',
                'direccion' => 'Calle Murillo 505',
                'telefono' => '2-3333333',
                'correo' => 'contacto@electricosmodernos.com'
            ],
            [
                'nit' => '44444444-4',
                'nombre_empresa' => 'Seguridad Total EIRL',
                'nombre_persona' => 'Patricia Vásquez',
                'direccion' => 'Av. Ballivián 201',
                'telefono' => '2-4444444',
                'correo' => 'ventas@seguridadtotal.com'
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
