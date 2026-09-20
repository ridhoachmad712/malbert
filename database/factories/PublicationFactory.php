<?php

namespace Database\Factories;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Publication>
 */
class PublicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'slug' => fake()->unique()->slug(),
            'content' => ['title' => fake()->sentence(), 'type' => 'kegiatan', 'summary' => fake()->sentence(), 'body' => fake()->paragraph(), 'event_date' => '2026-09-01', 'school_year' => '2026/2027', 'classes' => ['1'], 'cover' => null, 'media' => []],
            'status' => 'draft',
            'review_status' => 'draft',
        ];
    }
}
