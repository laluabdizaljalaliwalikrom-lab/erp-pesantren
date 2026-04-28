<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure target roles exist
        $rolesMap = [
            'admin' => 'super_admin',
            'treasurer' => 'bendahara_pesantren',
            'cashier' => 'kasir_pesantren',
        ];

        foreach (array_unique(array_values($rolesMap)) as $roleName) {
            Illuminate\Support\Facades\DB::table('roles')->updateOrInsert(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // 2. Migrate data
        $users = Illuminate\Support\Facades\DB::table('users')->whereNotNull('role')->get();

        foreach ($users as $user) {
            $oldRole = $user->role;
            if (isset($rolesMap[$oldRole])) {
                $role = Illuminate\Support\Facades\DB::table('roles')
                    ->where('name', $rolesMap[$oldRole])
                    ->where('guard_name', 'web')
                    ->first();

                if ($role) {
                    Illuminate\Support\Facades\DB::table('model_has_roles')->updateOrInsert([
                        'role_id' => $role->id,
                        'model_id' => $user->id,
                        'model_type' => 'App\Models\User',
                    ]);
                }
            }
        }

        // 3. Drop the static role column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable();
        });
        
        // Note: Down migration doesn't automatically restore data 
        // as the column was dropped.
    }
};
