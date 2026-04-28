<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('pesantren_name')->default('ERP Pesantren');
            $table->string('leader_name')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->text('footer_text')->nullable();
            $table->timestamps();
        });

        // Insert default row
        \Illuminate\Support\Facades\DB::table('app_settings')->insert([
            'id' => \Illuminate\Support\Str::uuid(),
            'pesantren_name' => 'ERP Pesantren',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
