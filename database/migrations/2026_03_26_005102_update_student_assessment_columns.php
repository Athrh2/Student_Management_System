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
        Schema::table('students', function (Blueprint $table) {
            $table->decimal('test_score', 5, 2)->default(0)->after('attendance_rate'); // 15%
            // keep assignment_score (25%)
            // add the Final Exam Prediction and Actual columns
            $table->decimal('predicted_final_score', 5, 2)->nullable(); // The AI's guess
            $table->decimal('actual_final_score', 5, 2)->nullable();    // Keyed in later
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            //
        });
    }
};
