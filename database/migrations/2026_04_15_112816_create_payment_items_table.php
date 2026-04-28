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
        Schema::create('payment_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignUuid('bill_id')->constrained('bills')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });

        // Update payments table to make bill_id nullable
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignUuid('bill_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_items');
        
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignUuid('bill_id')->nullable(false)->change();
        });
    }
};
