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
        Schema::table('students', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('id');
            $table->string('nisn')->nullable()->unique()->after('nis');
            $table->char('gender', 1)->nullable()->after('full_name');
            $table->string('father_name')->nullable()->after('address');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->string('guardian_phone')->nullable()->after('mother_name');
            
            // Update status enum
            $table->string('status')->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'photo',
                'nisn',
                'gender',
                'father_name',
                'mother_name',
                'guardian_phone',
            ]);
            
            // Revert status to original enum if possible
            $table->enum('status', ['active', 'graduated', 'resigned'])->default('active')->change();
        });
    }
};
