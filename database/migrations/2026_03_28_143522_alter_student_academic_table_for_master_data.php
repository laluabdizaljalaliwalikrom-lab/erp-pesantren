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
        Schema::table('student_academic', function (Blueprint $table) {
            $table->dropColumn(['academic_year', 'class_name']);
            $table->foreignUuid('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignUuid('school_class_id')->nullable()->constrained('classes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_academic', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['school_class_id']);
            $table->dropColumn(['academic_year_id', 'school_class_id']);
            $table->string('academic_year')->nullable();
            $table->string('class_name')->nullable();
        });
    }
};
