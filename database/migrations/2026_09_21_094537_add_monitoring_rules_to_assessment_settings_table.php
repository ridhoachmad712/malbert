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
        Schema::table('assessment_settings', function (Blueprint $table) {
            $table->string('monitoring_period', 20)->nullable()->after('repeat_rule');
            $table->string('student_score_method', 20)->nullable()->after('monitoring_period');
            $table->string('achievement_population', 20)->nullable()->after('student_score_method');
            $table->string('unassessed_rule', 20)->nullable()->after('achievement_population');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_settings', function (Blueprint $table) {
            $table->dropColumn(['monitoring_period', 'student_score_method', 'achievement_population', 'unassessed_rule']);
        });
    }
};
