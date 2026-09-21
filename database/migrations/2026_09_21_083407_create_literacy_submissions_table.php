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
        Schema::create('literacy_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('literacy_assignment_id')->constrained()->restrictOnDelete();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->date('journal_date')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->text('summary')->nullable();
            $table->string('work_type', 10)->default('none');
            $table->longText('work_text')->nullable();
            $table->string('work_url', 1000)->nullable();
            $table->string('work_path')->nullable();
            $table->string('work_original_name')->nullable();
            $table->text('reflection_understanding')->nullable();
            $table->text('reflection_feeling')->nullable();
            $table->text('reflection_commitment')->nullable();
            $table->string('status', 10)->default('draft');
            $table->unsignedInteger('revision_number')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->text('feedback')->nullable();
            $table->unique(['literacy_assignment_id', 'student_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('literacy_submissions');
    }
};
