<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing') || ! (SchoolSetting::values()['demo'] ?? false)) {
            return;
        }
        AcademicYear::firstOrCreate(['name' => '2026/2027'], ['starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'semester' => 'ganjil', 'is_current' => AcademicYear::where('is_current', true)->exists() ? null : true]);
    }
}
