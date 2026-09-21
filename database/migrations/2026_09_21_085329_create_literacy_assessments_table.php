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
        Schema::create('literacy_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('literacy_submission_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('classroom_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('assessed_by')->constrained('users')->restrictOnDelete();
            $table->string('semester', 6);
            $table->unsignedTinyInteger('focus_score');
            $table->unsignedTinyInteger('engagement_score');
            $table->unsignedTinyInteger('answer_score');
            $table->unsignedTinyInteger('retell_score');
            $table->unsignedTinyInteger('main_idea_score');
            $table->unsignedTinyInteger('summary_work_score');
            $table->unsignedTinyInteger('total_score');
            $table->decimal('score', 5, 2);
            $table->decimal('display_score', 5, 1);
            $table->string('category', 20);
            $table->text('feedback')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamp('assessed_at');
            $table->index(['classroom_id', 'academic_year_id', 'semester'], 'assessment_period_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('literacy_assessments');
    }
};
