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
            
            // Permisos de Clientes
            'view.clients',
            'create.clients',
            'edit.clients',
            'delete.clients',
            
            // Gestión de inventario
            'view.inventory',
            'create.inventory',
            'edit.inventory',
            
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
            'view.sales.reports',
            'view.inventory.reports', 
            'view.purchases.reports',
            'view.statistics',
            
            // Configuración del sistema
            'view.settings',
            'edit.settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $gerenteRole = Role::firstOrCreate(['name' => 'gerente']);
        $ventasRole = Role::firstOrCreate(['name' => 'ventas']);
        $almacenRole = Role::firstOrCreate(['name' => 'almacen']);
        $clienteRole = Role::firstOrCreate(['name' => 'cliente']);

        // ADMIN - Acceso total al sistema
        $adminRole->syncPermissions(Permission::all());

        // GERENTE - Manejo completo excepto configuración crítica del sistema
        $gerenteRole->syncPermissions([
            // Usuarios (solo visualizar y editar, no crear/eliminar)
            'view.users',
            'edit.users',
            
            // Productos - acceso completo
            'view.products',
            'create.products',
            'edit.products',
            'delete.products',
            
            // Categorías - acceso completo
            'view.categories',
            'create.categories',
            'edit.categories',
            'delete.categories',
            
            // Unidades de medida - acceso completo
            'view.measurements',
            'create.measurements',
            'edit.measurements',
            'delete.measurements',
            
            // Proveedores - acceso completo
            'view.suppliers',
            'create.suppliers',
            'edit.suppliers',
            'delete.suppliers',
            
            // Clientes - acceso completo
            'view.clients',
            'create.clients',
            'edit.clients',
            'delete.clients',
            
            // Inventario - acceso completo
            'view.inventory',
            'create.inventory',
            'edit.inventory',
            
            // Ventas - acceso completo
            'view.sales',
            'create.sales',
            'edit.sales',
            'delete.sales',
            
            // Compras - acceso completo
            'view.purchases',
            'create.purchases',
            'edit.purchases',
            'delete.purchases',
            
            // Reportes y estadísticas
            'view.reports',
            'view.sales.reports',
            'view.inventory.reports', 
            'view.purchases.reports',
            'view.statistics',
        ]);

        // VENTAS - Enfoque en atención al cliente y ventas
        $ventasRole->syncPermissions([
            // Productos (solo lectura)
            'view.products',
            
            // Categorías (solo lectura)
            'view.categories',
            
            // Clientes - gestión completa
            'view.clients',
            'create.clients',
            'edit.clients',
            
            // Inventario (solo consulta)
            'view.inventory',
            
            // Ventas - gestión completa
            'view.sales',
            'create.sales',
            'edit.sales',
            
            // Proveedores (solo consulta para referencias)
            'view.suppliers',
            
            // Reportes de ventas
            'view.reports',
            'view.sales.reports',
        ]);

        // ALMACEN - Enfoque en inventario y movimientos
        $almacenRole->syncPermissions([
            // Productos - gestión completa
            'view.products',
            'create.products',
            'edit.products',
            
            // Categorías - gestión para organización
            'view.categories',
            'create.categories',
            'edit.categories',
            
            // Unidades de medida
            'view.measurements',
            'create.measurements',
            'edit.measurements',
            
            // Proveedores - gestión completa
            'view.suppliers',
            'create.suppliers',
            'edit.suppliers',
            
            // Inventario - gestión completa
            'view.inventory',
            'create.inventory',
            'edit.inventory',
            
            // Compras - gestión completa
            'view.purchases',
            'create.purchases',
            'edit.purchases',
            
            // Ventas (solo lectura para verificar salidas)
            'view.sales',
            
            // Reportes de inventario
            'view.reports',
            'view.inventory.reports',
        ]);

        // CLIENTE - Solo para ventas online
        $clienteRole->syncPermissions([
            'view.products',
            'view.categories',
            'manage.own-sales',
        ]);
    }
}
