<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Super Admin Role
        $role = Role::firstOrCreate(['name' => 'super_admin']);

        // 2. Create Admin User
        $user = User::updateOrCreate(
            ['email' => 'admin@erp.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 3. Assign Role
        $user->assignRole($role);
    }
}
