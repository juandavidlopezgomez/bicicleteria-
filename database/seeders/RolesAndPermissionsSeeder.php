<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear permisos y roles (para evitar duplicados)
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Definir permisos por módulo
        $permissions = [
            // Módulo de Usuarios
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            // Módulo de Productos
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            // Módulo de Ventas
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
            // Módulo de Reportes
            'reports.view',
            'reports.generate',
        ];

        // Crear permisos
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        $propietario = Role::create(['name' => 'propietario']);
        $administrador = Role::create(['name' => 'administrador']);
        $cajero = Role::create(['name' => 'cajero']);

        // Asignar todos los permisos al propietario
        $propietario->syncPermissions($permissions);

        // Asignar permisos al administrador (todos menos eliminar usuarios)
        $administrador->syncPermissions([
            'users.view',
            'users.create',
            'users.edit',
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
            'reports.view',
            'reports.generate',
        ]);

        // Asignar permisos al cajero (solo ventas y ver productos)
        $cajero->syncPermissions([
            'products.view',
            'sales.view',
            'sales.create',
            'sales.edit',
        ]);

        // Asignar el rol de propietario al usuario de prueba (por ejemplo, adm1@gmail.com)
        $user = \App\Models\User::where('email', 'adm1@gmail.com')->first();
        if ($user) {
            $user->assignRole('propietario');
        }
    }
}