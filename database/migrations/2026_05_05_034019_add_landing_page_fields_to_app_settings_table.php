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
        Schema::table('app_settings', function (Blueprint $table) {
            $table->string('hero_title')->nullable()->after('pesantren_name');
            $table->text('hero_subtitle')->nullable()->after('hero_title');
            $table->text('vision')->nullable()->after('hero_subtitle');
            $table->text('mission')->nullable()->after('vision');
            $table->text('history')->nullable()->after('mission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['hero_title', 'hero_subtitle', 'vision', 'mission', 'history']);
        });
    }
};
