<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'orders.create',
            'orders.edit-own',
            'orders.view-own',
            'orders.view-site',
            'orders.edit-site',
            'orders.approve',
            'orders.view-all',
            'orders.fabricate',
            'reports.generate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $engineer = Role::firstOrCreate(['name' => 'engineer', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $workshop = Role::firstOrCreate(['name' => 'workshop', 'guard_name' => 'web']);

        $engineer->givePermissionTo(['orders.create', 'orders.edit-own', 'orders.view-own']);
        $manager->givePermissionTo(['orders.view-site', 'orders.edit-site', 'orders.approve']);
        $workshop->givePermissionTo(['orders.view-all', 'orders.fabricate', 'reports.generate']);
        $admin->givePermissionTo(Permission::all());
    }
}
