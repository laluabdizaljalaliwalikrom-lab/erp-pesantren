<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams = config('permission.teams');

        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
            $table->unsignedBigInteger($columnNames['permission_pivot_key'] ?? 'permission_id');
            $table->string('model_type');
            $table->uuid($columnNames['model_morph_key'] ?? 'model_id');
            $table->index([$columnNames['model_morph_key'] ?? 'model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($columnNames['permission_pivot_key'] ?? 'permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();

            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');
                $table->primary([$columnNames['team_foreign_key'], $columnNames['permission_pivot_key'] ?? 'permission_id', $columnNames['model_morph_key'] ?? 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
            } else {
                $table->primary([$columnNames['permission_pivot_key'] ?? 'permission_id', $columnNames['model_morph_key'] ?? 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
            }
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
            $table->unsignedBigInteger($columnNames['role_pivot_key'] ?? 'role_id');
            $table->string('model_type');
            $table->uuid($columnNames['model_morph_key'] ?? 'model_id');
            $table->index([$columnNames['model_morph_key'] ?? 'model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($columnNames['role_pivot_key'] ?? 'role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->cascadeOnDelete();

            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');
                $table->primary([$columnNames['team_foreign_key'], $columnNames['role_pivot_key'] ?? 'role_id', $columnNames['model_morph_key'] ?? 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
            } else {
                $table->primary([$columnNames['role_pivot_key'] ?? 'role_id', $columnNames['model_morph_key'] ?? 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $modelKey = $columnNames['model_morph_key'] ?? 'model_id';

        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) use ($modelKey) {
            $table->dropPrimary('model_has_permissions_permission_model_type_primary');
        });

        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) use ($modelKey) {
            $table->unsignedBigInteger($modelKey)->change();
            $table->primary(['permission_id', $modelKey, 'model_type'], 'model_has_permissions_permission_model_type_primary');
        });

        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($modelKey) {
            $table->dropPrimary('model_has_roles_role_model_type_primary');
        });

        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($modelKey) {
            $table->unsignedBigInteger($modelKey)->change();
            $table->primary(['role_id', $modelKey, 'model_type'], 'model_has_roles_role_model_type_primary');
        });
    }
};
