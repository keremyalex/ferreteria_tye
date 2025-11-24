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
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Gestión de productos
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // Gestión de categorías
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Gestión de proveedores
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            
            // Gestión de inventario
            'view inventory',
            'manage inventory',
            'view inventory movements',
            
            // Gestión de órdenes
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'manage own orders',
            
            // Reportes y estadísticas
            'view reports',
            'view statistics',
            
            // Configuración del sistema
            'view settings',
            'edit settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $customerRole = Role::create(['name' => 'customer']);
        $employeeRole = Role::create(['name' => 'employee']);
        $adminRole = Role::create(['name' => 'admin']);

        // Customer permissions
        $customerRole->givePermissionTo([
            'view products',
            'view categories',
            'manage own orders',
        ]);

        // Employee permissions
        $employeeRole->givePermissionTo([
            'view users',
            'view products',
            'create products',
            'edit products',
            'view categories',
            'create categories',
            'edit categories',
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'view inventory',
            'manage inventory',
            'view inventory movements',
            'view orders',
            'create orders',
            'edit orders',
            'view reports',
        ]);

        // Admin permissions (all)
        $adminRole->givePermissionTo(Permission::all());
    }
}
