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
            $table->dropForeign(['billing_event_id']);
            $table->dropColumn(['billing_event_id', 'original_amount', 'discount_amount', 'final_amount', 'paid_amount', 'bill_number']);

            $table->foreignUuid('fee_id')->nullable()->after('student_id')->constrained('fees')->cascadeOnDelete();
            $table->foreignUuid('academic_year_id')->nullable()->after('fee_id')->constrained('academic_years')->cascadeOnDelete();
            $table->decimal('amount', 15, 2)->after('academic_year_id');
            $table->date('due_date')->nullable()->after('amount');
            $table->string('period_name')->after('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['fee_id']);
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn(['fee_id', 'academic_year_id', 'amount', 'due_date', 'period_name']);

            $table->foreignUuid('billing_event_id')->nullable()->constrained('billing_events')->cascadeOnDelete();
            $table->string('bill_number')->nullable();
            $table->decimal('original_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('final_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
        });
    }
};
