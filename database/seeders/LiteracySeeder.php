<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\LiteracyAssignment;
use App\Models\ReadingResource;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class LiteracySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing') || ! (SchoolSetting::values()['demo'] ?? false)) {
            return;
        }
        $admin = User::where('email', 'admin@sekolah.test')->firstOrFail();
        $classroom = Classroom::where('name', 'I A (Contoh)')->with('academicYear')->firstOrFail();
        $reading = ReadingResource::firstOrCreate(['url' => 'https://buku.kemdikbud.go.id'], [
            'user_id' => $admin->id,
            'title' => 'Rak buku digital untuk latihan membaca',
            'description' => 'Contoh tautan bacaan. Periksa dan ganti sesuai pilihan sekolah.',
            'grade' => 1,
            'is_public' => true,
            'published_at' => now(),
        ]);
        LiteracyAssignment::firstOrCreate([
            'classroom_id' => $classroom->id,
            'title' => 'Cerita yang paling saya ingat',
        ], [
            'academic_year_id' => $classroom->academic_year_id,
            'reading_resource_id' => $reading->id,
            'created_by' => $admin->id,
            'instructions' => 'Baca satu cerita, tulis ringkasannya, lalu isi tiga pertanyaan refleksi.',
            'assigned_on' => '2026-08-03',
            'due_at' => '2026-08-10 12:00:00',
            'semester' => $classroom->academicYear->semester,
            'status' => 'active',
        ]);
    }
}
