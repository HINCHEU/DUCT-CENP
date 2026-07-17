<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Site;
use App\Models\UserSite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminSeeder::class,
            DuctTypesSeeder::class,
        ]);

        $site = Site::create(['name' => 'Example Site A']);

        $pm = User::create([
            'p_id' => 'PM001',
            'name' => 'Example Project Manager',
            'email' => 'pm@example.com',
            'password' => Hash::make('changeme'),
            'position' => 'Project Manager',
        ]);
        $pm->assignRole('manager');
        $site->update(['manager_id' => $pm->id]);

        UserSite::create([
            'user_id' => $pm->id,
            'site_id' => $site->id,
            'assigned_from' => now(),
        ]);
    }
}
