<?php

namespace Database\Factories;

use App\Models\SitePage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SitePage>
 */
class SitePageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'slug' => fake()->unique()->slug(),
            'body' => fake()->paragraphs(3, true),
            'is_published' => false,
        ];
    }
}
