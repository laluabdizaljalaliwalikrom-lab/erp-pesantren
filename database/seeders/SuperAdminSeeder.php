<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin']);
        
        $user = User::where('email', 'admin@erp.com')->first();
        
        if ($user) {
            $user->assignRole($role);
        }
    }
}
