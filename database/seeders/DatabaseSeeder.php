<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            MeasurementSeeder::class,
            CategorySeeder::class,
            SupplierSeeder::class,
            ClientSeeder::class,
            ProductInventorySeeder::class,
            PurchaseSeeder::class,
        ]);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@ferreteria.com',
        ]);
        $admin->assignRole('admin');

        // Create gerente user
        $gerente = User::factory()->create([
            'name' => 'Gerente General',
            'email' => 'gerente@ferreteria.com',
        ]);
        $gerente->assignRole('gerente');

        // Create ventas user
        $ventas = User::factory()->create([
            'name' => 'Vendedor Principal',
            'email' => 'ventas@ferreteria.com',
        ]);
        $ventas->assignRole('ventas');

        // Create almacen user
        $almacen = User::factory()->create([
            'name' => 'Encargado de Almacén',
            'email' => 'almacen@ferreteria.com',
        ]);
        $almacen->assignRole('almacen');

        // Create customer user
        $customer = User::factory()->create([
            'name' => 'Cliente Prueba',
            'email' => 'cliente@ferreteria.com',
        ]);
        $customer->assignRole('cliente');
    }
}
