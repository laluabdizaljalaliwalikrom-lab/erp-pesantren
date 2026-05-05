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
        Schema::table('students', function (Blueprint $col) {
            $col->string('nik', 16)->nullable()->after('nisn');
            $col->string('no_kk', 16)->nullable()->after('nik');
            $col->string('hp')->nullable()->after('mother_name');
            $col->string('email')->nullable()->after('hp');
            
            // Alamat Detail
            $col->string('rt', 5)->nullable()->after('address');
            $col->string('rw', 5)->nullable()->after('rt');
            $col->string('dusun')->nullable()->after('rw');
            $col->string('village')->nullable()->after('dusun');
            $col->string('district')->nullable()->after('village');
            
            // Latar Belakang
            $col->string('previous_school')->nullable()->after('district');
            $col->integer('sibling_position')->nullable()->after('previous_school');
            $col->integer('sibling_count')->nullable()->after('sibling_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $col) {
            $col->dropColumn([
                'nik', 'no_kk', 'hp', 'email', 
                'rt', 'rw', 'dusun', 'village', 'district', 
                'previous_school', 'sibling_position', 'sibling_count'
            ]);
        });
    }
};
