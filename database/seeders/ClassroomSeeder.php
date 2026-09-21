<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing') || ! (SchoolSetting::values()['demo'] ?? false)) {
            return;
        }
        $year = AcademicYear::where('name', '2026/2027')->firstOrFail();
        $teacher = User::where('email', 'guru@sekolah.test')->where('role', 'guru')->where('active', true)->first();
        foreach (['I A (Contoh)', 'I B (Contoh)'] as $name) {
            Classroom::firstOrCreate(['academic_year_id' => $year->id, 'name' => $name], ['grade' => 1, 'homeroom_teacher_id' => $name === 'I A (Contoh)' ? $teacher?->id : null]);
        }
    }
}
