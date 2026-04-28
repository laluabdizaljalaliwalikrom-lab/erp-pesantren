<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            // Ensure relations are present using foreignUuid
            if (!Schema::hasColumn('bills', 'student_id')) {
                $table->foreignUuid('student_id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('bills', 'billing_event_id')) {
                $table->foreignUuid('billing_event_id')->constrained()->cascadeOnDelete();
            }

            // Add the missing bill_number column
            if (!Schema::hasColumn('bills', 'bill_number')) {
                $table->string('bill_number')->unique()->after('billing_event_id');
            }

            // Enforce precision 15,2 on decimal columns
            $table->decimal('original_amount', 15, 2)->change();
            $table->decimal('discount_amount', 15, 2)->default(0)->change();
            $table->decimal('final_amount', 15, 2)->change();
            $table->decimal('paid_amount', 15, 2)->default(0)->change();

            // Ensure SoftDeletes exist
            if (!Schema::hasColumn('bills', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('bill_number');
            $table->decimal('original_amount', 12, 2)->change();
            $table->decimal('discount_amount', 12, 2)->default(0)->change();
            $table->decimal('final_amount', 12, 2)->change();
            $table->decimal('paid_amount', 12, 2)->default(0)->change();
        });
    }
};