<?php

namespace App\Console\Commands;

use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Str;

#[Signature('school:release-check')]
#[Description('Check configuration, data, storage, migrations, and backup scheduling before launch')]
class SchoolReleaseCheck extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(Migrator $migrator, Schedule $schedule): int
    {
        $school = SchoolSetting::values();
        $migrationFiles = array_keys($migrator->getMigrationFiles(database_path('migrations')));
        $ranMigrations = $migrator->repositoryExists() ? $migrator->getRepository()->getRan() : [];
        $storageRoot = config('filesystems.disks.local.root');
        $checks = [
            ['Lingkungan produksi', config('app.env') === 'production', 'Atur APP_ENV=production.'],
            ['Mode debug dimatikan', config('app.debug') === false, 'Atur APP_DEBUG=false.'],
            ['URL menggunakan HTTPS', Str::startsWith((string) config('app.url'), 'https://'), 'Atur APP_URL ke domain HTTPS sekolah.'],
            ['Kunci aplikasi tersedia', filled(config('app.key')), 'Buat APP_KEY unik pada server produksi.'],
            ['Cookie sesi aman', config('session.secure') === true, 'Atur SESSION_SECURE_COOKIE=true.'],
            ['Isi sesi terenkripsi', config('session.encrypt') === true, 'Atur SESSION_ENCRYPT=true.'],
            ['Database sudah dimigrasikan', $migrator->repositoryExists() && array_diff($migrationFiles, $ranMigrations) === [], 'Jalankan php artisan migrate --force.'],
            ['Penyimpanan privat dapat ditulis', is_string($storageRoot) && is_dir($storageRoot) && is_writable($storageRoot), 'Siapkan izin tulis untuk storage/app/private.'],
            ['Cadangan dijadwalkan', collect($schedule->events())->contains(fn ($event) => Str::contains((string) $event->command, 'school:backup')), 'Daftarkan school:backup pada scheduler.'],
            ['Google Drive dikonfigurasi', filled(config('services.drive.api_key')), 'Isi GOOGLE_DRIVE_API_KEY pada server.'],
            ['Profil sekolah bukan data contoh', ! ($school['demo'] ?? true) && ($school['name'] ?? '') !== 'Sekolah Kita' && filled($school['address'] ?? null) && filled($school['contact'] ?? null), 'Lengkapi nama, alamat, kontak, lalu matikan penanda data contoh.'],
            ['Akun demo sudah ditangani', ! User::query()->whereIn('email', ['admin@sekolah.test', 'guru@sekolah.test'])->exists(), 'Hapus atau nonaktifkan akun demo setelah akun nyata diuji.'],
            ['Admin aktif tersedia', User::query()->where('role', 'admin')->where('active', true)->exists(), 'Siapkan minimal satu akun admin aktif.'],
            ['Guru aktif tersedia', User::query()->where('role', 'guru')->where('active', true)->exists(), 'Siapkan minimal satu akun guru aktif.'],
        ];

        $this->table(
            ['Pemeriksaan', 'Status', 'Tindakan bila belum siap'],
            array_map(fn (array $check): array => [$check[0], $check[1] ? 'SIAP' : 'BELUM', $check[1] ? '' : $check[2]], $checks),
        );

        $failures = collect($checks)->reject(fn (array $check) => $check[1]);
        if ($failures->isNotEmpty()) {
            $this->error($failures->count().' pemeriksaan belum siap. Perbaiki semua baris BELUM sebelum peluncuran.');

            return self::FAILURE;
        }

        $this->info('Pemeriksaan aplikasi lulus. Pastikan cron scheduler aktif, backup tersalin keluar server, UAT sekolah ditandatangani, dan DNS/HTTPS diuji pada hosting nyata.');

        return self::SUCCESS;
    }
}
