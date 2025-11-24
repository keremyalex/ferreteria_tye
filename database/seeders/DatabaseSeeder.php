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

        // Create employee user
        $employee = User::factory()->create([
            'name' => 'Empleado',
            'email' => 'empleado@ferreteria.com',
        ]);
        $employee->assignRole('employee');

        // Create customer user
        $customer = User::factory()->create([
            'name' => 'Cliente Prueba',
            'email' => 'cliente@ejemplo.com',
        ]);
        $customer->assignRole('customer');
    }
}
