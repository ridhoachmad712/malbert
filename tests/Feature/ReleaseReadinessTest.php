<?php

namespace Tests\Feature;

use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReleaseReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_release_check_reports_unfinished_local_environment(): void
    {
        $this->artisan('school:release-check')
            ->expectsOutputToContain('pemeriksaan belum siap')
            ->assertFailed();
    }

    public function test_release_check_passes_when_production_requirements_are_complete(): void
    {
        Storage::fake('local');
        config([
            'app.env' => 'production',
            'app.debug' => false,
            'app.url' => 'https://cerita.sekolah.sch.id',
            'app.key' => 'base64:release-check-key',
            'session.secure' => true,
            'session.encrypt' => true,
            'services.drive.api_key' => 'drive-key-for-test',
        ]);
        SchoolSetting::create(['data' => [
            'name' => 'SD Contoh',
            'npsn' => '12345678',
            'tagline' => 'Belajar bersama',
            'address' => 'Alamat sekolah',
            'email' => 'sekolah@example.sch.id',
            'phone' => '+62 812-3456-7890',
            'demo' => false,
        ]]);
        User::factory()->create(['email' => 'admin@contoh.sch.id', 'role' => 'admin', 'active' => true]);
        User::factory()->create(['email' => 'guru@contoh.sch.id', 'role' => 'guru', 'active' => true]);

        $this->artisan('school:release-check')
            ->expectsOutputToContain('Pemeriksaan aplikasi lulus')
            ->assertSuccessful();
    }
}
