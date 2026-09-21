<?php

namespace Database\Seeders;

use App\Models\AssessmentSetting;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class EvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing') || ! (SchoolSetting::values()['demo'] ?? false)) {
            return;
        }
        $admin = User::where('email', 'admin@sekolah.test')->firstOrFail();
        AssessmentSetting::firstOrCreate(['id' => 1], [
            'configured_by' => $admin->id,
            'score_rounding' => 'whole',
            'success_threshold' => 76,
            'point_period' => 'month',
            'repeat_rule' => 'once_daily',
            'monitoring_period' => 'month',
            'student_score_method' => 'average',
            'achievement_population' => 'enrolled',
            'unassessed_rule' => 'not_successful',
            'configured_at' => now(),
        ]);
    }
}
