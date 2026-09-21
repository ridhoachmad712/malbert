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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 9)->unique();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->boolean('is_current')->nullable()->unique();
            $table->string('semester', 6)->default('ganjil');
            $table->timestamps();
        });
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->string('name', 40);
            $table->unsignedTinyInteger('grade');
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->unique(['academic_year_id', 'name']);
            $table->timestamps();
        });
        Schema::create('classroom_user', function (Blueprint $table) {
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['classroom_id', 'user_id']);
        });
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('nis', 30)->nullable()->unique();
            $table->string('nisn', 10)->nullable()->unique();
            $table->foreignId('classroom_id')->constrained()->restrictOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('classroom_id')->constrained()->restrictOnDelete();
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->index(['student_id', 'ends_on']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('students');
        Schema::dropIfExists('classroom_user');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('academic_years');
    }
};
