<?php

namespace Database\Factories;

use App\Models\NavigationItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NavigationItem>
 */
class NavigationItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => fake()->words(2, true),
            'type' => 'internal',
            'target' => 'home',
            'sort_order' => 50,
            'is_visible' => true,
            'open_new_tab' => false,
        ];
    }
}
