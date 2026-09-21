<?php

namespace Database\Factories;

use App\Models\LiteracyReview;
use App\Models\LiteracySubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LiteracyReview>
 */
class LiteracyReviewFactory extends Factory
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
            'user_id' => User::factory(),
            'action' => 'submit',
            'snapshot' => [],
        ];
    }
}
