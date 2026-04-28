<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nis')->unique();
            $table->string('full_name');
            $table->enum('residency_status', ['mukim', 'tidak_mukim'])->default('mukim');
            $table->enum('special_condition', ['none', 'yatim', 'piatu', 'yatim_piatu', 'kurang_mampu'])->default('none');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};