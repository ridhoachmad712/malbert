<?php

namespace Database\Factories;

use App\Models\AssessmentSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentSetting>
 */
class AssessmentSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'configured_by' => User::factory()->state(['role' => 'admin']),
            'score_rounding' => 'whole',
            'success_threshold' => 76,
            'point_period' => 'month',
            'repeat_rule' => 'once_daily',
            'configured_at' => now(),
        ];
    }
}
