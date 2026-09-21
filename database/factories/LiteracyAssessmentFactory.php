<?php

namespace Database\Factories;

use App\Models\LiteracyAssessment;
use App\Models\LiteracySubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LiteracyAssessment>
 */
class LiteracyAssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'literacy_submission_id' => LiteracySubmission::factory(),
            'student_id' => fn (array $attributes) => LiteracySubmission::findOrFail($attributes['literacy_submission_id'])->student_id,
            'classroom_id' => fn (array $attributes) => LiteracySubmission::findOrFail($attributes['literacy_submission_id'])->assignment->classroom_id,
            'academic_year_id' => fn (array $attributes) => LiteracySubmission::findOrFail($attributes['literacy_submission_id'])->assignment->academic_year_id,
            'assessed_by' => User::factory(),
            'semester' => 'ganjil',
            'focus_score' => 3,
            'engagement_score' => 3,
            'answer_score' => 3,
            'retell_score' => 3,
            'main_idea_score' => 3,
            'summary_work_score' => 3,
            'total_score' => 18,
            'score' => 75,
            'display_score' => 75,
            'category' => 'fair',
            'version' => 1,
            'assessed_at' => now(),
        ];
    }
}
