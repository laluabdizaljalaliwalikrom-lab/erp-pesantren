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
        Schema::table('bills', function (Blueprint $table) {
            $table->decimal('original_amount', 15, 2)->default(0)->after('academic_year_id');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('original_amount');
            $table->decimal('final_amount', 15, 2)->default(0)->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn(['original_amount', 'discount_amount', 'final_amount']);
        });
    }
};
