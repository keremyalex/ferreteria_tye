<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Gestión de usuarios
            'view.users',
            'create.users',
            'edit.users',
            'delete.users',
            
            // Gestión de productos
            'view.products',
            'create.products',
            'edit.products',
            'delete.products',
            
            // Gestión de categorías
            'view.categories',
            'create.categories',
            'edit.categories',
            'delete.categories',
            
            // Gestión de unidades de medida
            'view.measurements',
            'create.measurements',
            'edit.measurements',
            'delete.measurements',
            
            // Gestión de proveedores
            'view.suppliers',
            'create.suppliers',
            'edit.suppliers',
            'delete.suppliers',
            
            // Gestión de inventario
            'view.inventory',
            'manage.inventory',
            'view.inventory-movements',
            
            // Gestión de ventas
            'view.sales',
            'create.sales',
            'edit.sales',
            'delete.sales',
            'manage.own-sales',
            
            // Gestión de compras
            'view.purchases',
            'create.purchases',
            'edit.purchases',
            'delete.purchases',
            
            // Reportes y estadísticas
            'view.reports',
            'view.statistics',
            
            // Configuración del sistema
            'view.settings',
            'edit.settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Customer permissions
        $customerRole->syncPermissions([
            'view.products',
            'view.categories',
            'manage.own-sales',
        ]);

        // Employee permissions
        $employeeRole->syncPermissions([
            'view.users',
            'view.products',
            'create.products',
            'edit.products',
            'view.categories',
            'create.categories',
            'edit.categories',
            'view.measurements',
            'create.measurements',
            'edit.measurements',
            'view.suppliers',
            'create.suppliers',
            'edit.suppliers',
            'view.inventory',
            'manage.inventory',
            'view.inventory-movements',
            'view.sales',
            'create.sales',
            'edit.sales',
            'view.purchases',
            'create.purchases',
            'edit.purchases',
            'view.reports',
        ]);

        // Admin permissions (all)
        $adminRole->syncPermissions(Permission::all());
    }
}
