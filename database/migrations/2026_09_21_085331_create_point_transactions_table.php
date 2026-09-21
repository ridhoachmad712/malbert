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
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('classroom_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('literacy_submission_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('adjusts_transaction_id')->nullable()->constrained('point_transactions')->restrictOnDelete();
            $table->string('semester', 6);
            $table->string('kind', 10);
            $table->string('activity_code', 40);
            $table->smallInteger('points');
            $table->date('occurred_on');
            $table->string('period_key', 40);
            $table->string('source_key', 100);
            $table->text('reason')->nullable();
            $table->json('snapshot');
            $table->unique(['student_id', 'source_key', 'activity_code']);
            $table->index(['student_id', 'period_key']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
