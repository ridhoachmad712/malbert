<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SchoolCreateAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_admin_is_created_with_hashed_password(): void
    {
        $this->artisan('school:create-admin')
            ->expectsQuestion('Nama admin', 'Admin Sekolah')
            ->expectsQuestion('Email admin', 'admin@sekolah.sch.id')
            ->expectsQuestion('Kata sandi admin (minimal 12 karakter)', 'sandi-rahasia-aman')
            ->expectsQuestion('Ulangi kata sandi admin', 'sandi-rahasia-aman')
            ->expectsOutputToContain('Akun admin berhasil dibuat')
            ->assertSuccessful();

        $admin = User::query()->sole();
        $this->assertSame('admin', $admin->role);
        $this->assertTrue($admin->active);
        $this->assertTrue(Hash::check('sandi-rahasia-aman', $admin->password));
    }

    public function test_existing_admin_prevents_bootstrap_command_from_creating_another(): void
    {
        User::factory()->create(['role' => 'admin']);

        $this->artisan('school:create-admin')
            ->expectsOutputToContain('Akun admin sudah ada')
            ->assertFailed();

        $this->assertSame(1, User::query()->count());
    }

    public function test_invalid_password_does_not_create_admin(): void
    {
        $this->artisan('school:create-admin')
            ->expectsQuestion('Nama admin', 'Admin Sekolah')
            ->expectsQuestion('Email admin', 'admin@sekolah.sch.id')
            ->expectsQuestion('Kata sandi admin (minimal 12 karakter)', 'pendek')
            ->expectsQuestion('Ulangi kata sandi admin', 'pendek')
            ->assertFailed();

        $this->assertSame(0, User::query()->count());
    }
}
