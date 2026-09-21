<?php

namespace Database\Factories;

use App\Models\LiteracyAssignment;
use App\Models\LiteracySubmission;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LiteracySubmission>
 */
class LiteracySubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'literacy_assignment_id' => LiteracyAssignment::factory(),
            'student_id' => Student::factory(),
            'recorded_by' => User::factory(),
            'journal_date' => '2026-08-02',
            'duration_minutes' => 20,
            'summary' => fake()->paragraph(),
            'work_type' => 'none',
            'reflection_understanding' => fake()->sentence(),
            'reflection_feeling' => fake()->sentence(),
            'reflection_commitment' => fake()->sentence(),
            'status' => 'draft',
        ];
    }
}
