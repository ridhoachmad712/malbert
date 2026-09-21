<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\PointTransaction;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PointTransaction>
 */
class PointTransactionFactory extends Factory
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
            'academic_year_id' => fn (array $attributes) => Classroom::findOrFail($attributes['classroom_id'])->academic_year_id,
            'recorded_by' => User::factory(),
            'semester' => 'ganjil',
            'kind' => 'earn',
            'activity_code' => 'read_15',
            'points' => 5,
            'occurred_on' => '2026-09-21',
            'period_key' => '2026-09',
            'source_key' => fn () => 'manual:'.Str::uuid(),
            'snapshot' => ['label' => 'Membaca 15 menit', 'points' => 5],
        ];
    }
}
