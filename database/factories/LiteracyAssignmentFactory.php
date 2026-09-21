<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\LiteracyAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LiteracyAssignment>
 */
class LiteracyAssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'classroom_id' => Classroom::factory(),
            'academic_year_id' => fn (array $attributes) => Classroom::findOrFail($attributes['classroom_id'])->academic_year_id,
            'created_by' => User::factory(),
            'title' => fake()->sentence(4),
            'instructions' => fake()->paragraph(),
            'assigned_on' => '2026-08-01',
            'semester' => 'ganjil',
            'status' => 'active',
        ];
    }
}
