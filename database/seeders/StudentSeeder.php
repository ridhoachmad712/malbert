<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing') || ! (SchoolSetting::values()['demo'] ?? false)) {
            return;
        }
        $classroom = Classroom::whereHas('academicYear', fn ($query) => $query->where('name', '2026/2027'))->where('name', 'I A (Contoh)')->firstOrFail();
        foreach (['Murid Contoh A', 'Murid Contoh B', 'Murid Contoh C'] as $index => $name) {
            Student::firstOrCreate(['nis' => '99000'.($index + 1)], ['name' => $name, 'classroom_id' => $classroom->id, 'active' => true]);
        }
    }
}
