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
        Schema::create('assessment_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('configured_by')->constrained('users')->restrictOnDelete();
            $table->string('score_rounding', 20);
            $table->decimal('success_threshold', 5, 2);
            $table->string('point_period', 20);
            $table->string('repeat_rule', 20);
            $table->timestamp('configured_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_settings');
    }
};
