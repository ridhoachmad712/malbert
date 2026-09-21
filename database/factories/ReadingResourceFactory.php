<?php

namespace Database\Factories;

use App\Models\ReadingResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReadingResource>
 */
class ReadingResourceFactory extends Factory
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
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(),
            'url' => 'https://example.com/bacaan/'.fake()->uuid(),
            'grade' => fake()->numberBetween(1, 6),
            'is_public' => false,
        ];
    }
}
