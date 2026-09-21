<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = fake()->unique()->numberBetween(2000, 2090);

        return ['name' => $year.'/'.($year + 1), 'starts_on' => $year.'-07-01', 'ends_on' => ($year + 1).'-06-30', 'semester' => 'ganjil', 'is_current' => null];
    }
}
