<?php

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        SchoolSetting::firstOrCreate(['id' => 1], ['data' => ['name' => 'Sekolah Kita', 'tagline' => 'Tempat tumbuh, berkarya, dan bercerita.', 'description' => 'Setiap anak memiliki rasa ingin tahu, bakat, dan cerita yang berharga. Ruang ini merayakan perjalanan belajar mereka bersama teman dan guru.', 'vision' => 'Anak yang ingin tahu, percaya diri, kreatif, dan peduli.', 'mission' => "Menghadirkan pembelajaran yang menyenangkan.\nMemberi ruang bagi setiap anak untuk berkarya.\nMenumbuhkan kepedulian dan kebiasaan baik.", 'address' => '', 'contact' => '', 'demo' => true]]);
        if (! app()->environment('local', 'testing')) {
            return;
        }
        $admin = User::firstOrCreate(['email' => 'admin@sekolah.test'], ['name' => 'Admin Sekolah', 'password' => env('DEMO_ADMIN_PASSWORD', Str::password(20)), 'role' => 'admin', 'active' => true]);
        User::firstOrCreate(['email' => 'guru@sekolah.test'], ['name' => 'Guru Kelas', 'password' => env('DEMO_TEACHER_PASSWORD', Str::password(20)), 'role' => 'guru', 'active' => true]);
        $examples = [
            ['kegiatan', 'Tangan kecil, perubahan besar', 'Belajar merawat bumi dimulai dari kebun kecil kita.', 'Di kebun sekolah, siswa mengenal benih, menyiapkan tanah, dan belajar merawat tanaman bersama. Setiap kelompok mendapat kesempatan mencatat pertumbuhan tanamannya.', ['3', '4'], '🌱'],
            ['karya', 'Warna-warni imajinasi kita', 'Dari selembar kertas, lahir dunia yang penuh warna.', 'Anak-anak menuangkan imajinasi melalui gambar dan warna. Setiap karya menjadi kesempatan untuk berbagi cerita dan menghargai sudut pandang teman.', ['2'], '🎨'],
            ['prestasi', 'Berani mencoba, bangga berkarya', 'Merayakan semangat dan usaha dalam pekan kreativitas.', 'Berlatih, saling mendukung, dan berani tampil adalah bagian penting dari pencapaian siswa. Ruang prestasi ini dapat diisi dengan pencapaian nyata sekolah. Contoh ini tidak menyatakan hasil lomba yang sebenarnya.', ['5', '6'], '🏆'],
            ['kegiatan', 'Petualangan di antara halaman buku', 'Membaca bersama dan menemukan cerita baru setiap hari.', 'Melalui kegiatan membaca bersama, siswa bergantian menceritakan tokoh favorit dan mendengarkan pendapat teman.', ['1', '2'], '📚'],
            ['karya', 'Dari barang bekas menjadi karya', 'Membuat sesuatu yang baru dengan ide sederhana.', 'Proyek kelompok mengajak siswa melihat kemungkinan baru pada bahan yang dapat digunakan kembali.', ['4'], '✂️'],
            ['kegiatan', 'Bersama teman, belajar jadi seru', 'Satu permainan, banyak pelajaran tentang kerja sama.', 'Permainan kelompok memberi ruang untuk belajar mengikuti aturan, membantu teman, dan menyelesaikan tantangan bersama.', ['3'], '⚽'],
        ];
        foreach ($examples as $i => [$type, $title, $summary, $body, $classes, $emoji]) {
            $content = ['title' => $title, 'type' => $type, 'summary' => $summary, 'body' => $body."\n\nContoh publikasi: ganti cerita ini dengan dokumentasi kegiatan sekolah Anda.", 'event_date' => now()->subDays($i * 2)->toDateString(), 'school_year' => '2026/2027', 'classes' => $classes, 'cover' => null, 'media' => [], 'emoji' => $emoji, 'example' => true, 'permission_confirmed' => true];
            Publication::firstOrCreate(['slug' => 'contoh-'.($i + 1)], ['user_id' => $admin->id, 'content' => $content, 'published_content' => $content, 'status' => 'published', 'review_status' => 'approved', 'featured' => $i === 0, 'published_at' => now()->subDays($i)]);
        }
    }
}
