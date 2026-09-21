<?php

namespace Database\Seeders;

use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing') || ! (SchoolSetting::values()['demo'] ?? false)) {
            return;
        }
        foreach (Student::whereIn('nis', ['990001', '990002', '990003'])->where('name', 'like', 'Murid Contoh %')->get() as $student) {
            if (! $student->enrollments()->exists()) {
                $student->enrollments()->create(['classroom_id' => $student->classroom_id, 'starts_on' => '2026-07-01']);
            }
        }
    }
}
