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
            $table->string('director_name')->nullable()->after('history');
            $table->string('director_title')->nullable()->after('director_name');
            $table->text('director_greeting')->nullable()->after('director_title');
            $table->string('director_image_path')->nullable()->after('director_greeting');
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['director_name', 'director_title', 'director_greeting', 'director_image_path']);
        });
    }
};
