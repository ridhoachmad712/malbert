<?php

namespace Database\Factories;

use App\Models\LiteracyAssessment;
use App\Models\LiteracyAssessmentRevision;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LiteracyAssessmentRevision>
 */
class LiteracyAssessmentRevisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'literacy_assessment_id' => LiteracyAssessment::factory(),
            'user_id' => User::factory(),
            'version' => 1,
            'action' => 'create',
            'snapshot' => [],
        ];
    }
}
