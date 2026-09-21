<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'classroom_id' => fn (array $attributes) => Student::findOrFail($attributes['student_id'])->classroom_id,
            'starts_on' => fn (array $attributes) => Classroom::findOrFail($attributes['classroom_id'])->academicYear->starts_on,
            'ends_on' => null,
        ];
    }
}
