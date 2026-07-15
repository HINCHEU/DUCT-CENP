<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'p_id' => 'ADMIN001',
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'position' => 'Admin'
            ]
        );

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->assignRole('admin');
        
        if (Role::where('name', 'super_admin')->exists()) {
            $admin->assignRole('super_admin');
        }
    }
}
