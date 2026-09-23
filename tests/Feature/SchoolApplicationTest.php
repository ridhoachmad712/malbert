<?php

namespace Tests\Feature;

use App\Models\Publication;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SchoolApplicationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_replace(['title' => 'Kebun Kelas Tiga', 'type' => 'kegiatan', 'summary' => 'Belajar merawat tanaman.', 'body' => 'Siswa menyiram tanaman bersama.', 'event_date' => '2026-09-19', 'school_year' => '2026/2027', 'classes' => ['3'], 'intent' => 'submit', 'permission_confirmed' => '1'], $overrides);
    }

    public function test_guest_only_sees_published_content_and_filters_work(): void
    {
        $draft = Publication::factory()->create();
        $published = Publication::factory()->create(['content' => $this->payload(['media' => [], 'cover' => null])]);
        $published->update(['published_content' => $published->content, 'status' => 'published', 'review_status' => 'approved', 'published_at' => now()]);
        $this->get('/')->assertOk()->assertSee('Kebun Kelas Tiga')->assertDontSee($draft->content['title']);
        $this->get('/?class=3&type=kegiatan&year=2026%2F2027&q=Kebun')->assertOk()->assertSee('Kebun Kelas Tiga');
        $this->get('/?class=1')->assertOk()->assertDontSee('Kebun Kelas Tiga');
        $this->get(route('story', $draft->slug))->assertNotFound();
        $this->get(route('publications.preview', $draft))->assertRedirect(route('login'));
    }

    public function test_teacher_cannot_edit_others_or_publish_or_manage_users(): void
    {
        $teacher = User::factory()->create();
        $other = Publication::factory()->create();
        $this->actingAs($teacher)->get(route('publications.edit', $other))->assertForbidden();
        $this->put(route('publications.update', $other), $this->payload())->assertForbidden();
        $this->get(route('publications.preview', $other))->assertForbidden();
        $this->post(route('publications.review', $other), ['action' => 'publish', 'media_checked' => 1])->assertForbidden();
        $this->get(route('users.index'))->assertForbidden();
        $this->get(route('settings'))->assertForbidden();
    }

    public function test_submit_publish_revise_return_republish_and_archive(): void
    {
        $teacher = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($teacher)->post(route('publications.store'), $this->payload())->assertSessionHasNoErrors()->assertRedirect();
        $post = Publication::firstOrFail();
        $this->assertSame('pending', $post->status);
        $this->actingAs($admin)->post(route('publications.review', $post), ['action' => 'publish', 'media_checked' => 1])->assertSessionHasNoErrors();
        $this->assertSame('published', $post->fresh()->status);
        $this->actingAs($teacher)->put(route('publications.update', $post), $this->payload(['title' => 'Revisi Rahasia']))->assertSessionHasNoErrors();
        $this->get(route('story', $post->slug))->assertSee('Kebun Kelas Tiga')->assertDontSee('Revisi Rahasia');
        $this->actingAs($admin)->post(route('publications.review', $post), ['action' => 'return', 'note' => 'Lengkapi keterangan.'])->assertSessionHasNoErrors();
        $this->assertSame('changes', $post->fresh()->review_status);
        $this->assertSame('published', $post->fresh()->status);
        $this->actingAs($teacher)->put(route('publications.update', $post), $this->payload(['title' => 'Revisi Disetujui']))->assertSessionHasNoErrors();
        $this->actingAs($admin)->post(route('publications.review', $post), ['action' => 'publish', 'media_checked' => 1])->assertSessionHasNoErrors();
        $this->get(route('story', $post->slug))->assertSee('Revisi Disetujui');
        $this->post(route('publications.review', $post), ['action' => 'archive'])->assertSessionHasNoErrors();
        $this->get(route('story', $post->slug))->assertNotFound();
    }

    public function test_images_are_compressed_and_draft_media_is_private(): void
    {
        Storage::fake('local');
        $teacher = User::factory()->create();
        $this->actingAs($teacher)->post(route('publications.store'), $this->payload(['cover' => UploadedFile::fake()->image('photo.jpg', 2400, 1600), 'photos' => [UploadedFile::fake()->image('gallery.png', 800, 600)]]))->assertSessionHasNoErrors();
        $post = Publication::firstOrFail();
        $path = $post->content['cover'];
        Storage::disk('local')->assertExists($path);
        $this->assertSame('image/webp', mime_content_type(Storage::disk('local')->path($path)));
        $this->assertLessThanOrEqual(1200, getimagesize(Storage::disk('local')->path($path))[0]);
        $this->get($post->mediaUrl($path))->assertOk();
        auth()->logout();
        $this->get($post->mediaUrl($path))->assertNotFound();
        $post->update(['status' => 'published', 'published_content' => $post->content]);
        $this->get($post->mediaUrl($path))->assertOk();
        $post->update(['status' => 'archived']);
        $this->get($post->mediaUrl($path))->assertNotFound();
    }

    public function test_drive_urls_are_normalized_and_unsafe_sources_rejected(): void
    {
        $teacher = User::factory()->create();
        $this->actingAs($teacher)->post(route('publications.store'), $this->payload(['media' => [['kind' => 'video', 'url' => 'https://drive.google.com/file/d/Abcdefghij12345/view?resourcekey=0-secretkey', 'caption' => 'Video belajar']]]))->assertSessionHasNoErrors();
        $this->assertSame('https://drive.google.com/file/d/Abcdefghij12345/preview?resourcekey=0-secretkey', Publication::first()->content['media'][0]['embed']);
        foreach (['https://drive.google.com.evil.test/file/d/Abcdefghij12345/view', 'https://drive.google.com/drive/folders/Abcdefghij12345', 'http://127.0.0.1/private', 'javascript:alert(1)'] as $url) {
            $this->post(route('publications.store'), $this->payload(['media' => [['kind' => 'video', 'url' => $url]]]))->assertSessionHasErrors('media.0.url');
        }
        $this->assertSame(1, Publication::count());
    }

    public function test_foreign_media_cannot_be_attached_to_own_post(): void
    {
        $teacher = User::factory()->create();
        $this->actingAs($teacher)->post(route('publications.store'), $this->payload(['existing' => [['path' => 'media/stolen.webp']]]))->assertStatus(422);
        $this->assertSame(0, Publication::count());
    }

    public function test_disabled_user_cannot_login_or_continue_session(): void
    {
        $user = User::factory()->create(['active' => false, 'password' => 'KnownPassword123']);
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'KnownPassword123'])->assertSessionHasErrors('email');
        $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_login_logout_and_password_change(): void
    {
        $user = User::factory()->create(['password' => 'KnownPassword123']);
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'KnownPassword123'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->put(route('password.update'), ['current_password' => 'KnownPassword123', 'password' => 'NewPassword12345', 'password_confirmation' => 'NewPassword12345'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewPassword12345', $user->fresh()->password));
        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_admin_cannot_disable_self_and_can_create_teacher(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('users.store'), ['name' => 'Guru Baru', 'email' => 'baru@sekolah.test', 'password' => 'PasswordGuru123', 'role' => 'guru', 'active' => 1])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['email' => 'baru@sekolah.test', 'role' => 'guru']);
        $this->put(route('users.update', $admin), ['name' => $admin->name, 'email' => $admin->email, 'role' => 'guru', 'active' => 0])->assertSessionHasErrors('active');
        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_all_management_pages_render(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $post = Publication::factory()->create();
        $this->actingAs($admin);
        foreach (['dashboard', 'publications.create', 'users.index', 'settings', 'account'] as $name) {
            $this->get(route($name))->assertOk();
        }
        $this->get(route('publications.edit', $post))->assertOk();
        $this->get(route('publications.preview', $post))->assertOk();
        $this->get(route('profile'))->assertOk();
    }

    public function test_backup_round_trip_restores_records_and_media(): void
    {
        Storage::fake('local');
        $post = Publication::factory()->create();
        Storage::disk('local')->put('media/test-image.webp', 'fixture-image');
        $this->artisan('school:backup')->assertSuccessful();
        $backup = Storage::disk('local')->files('backups')[0];
        $this->artisan('school:backup', ['--restore' => Storage::disk('local')->path($backup)])->assertFailed();
        Publication::query()->delete();
        User::query()->delete();
        Storage::disk('local')->delete('media/test-image.webp');
        $this->artisan('school:backup', ['--restore' => Storage::disk('local')->path($backup)])->assertSuccessful();
        $this->assertDatabaseHas('publications', ['slug' => $post->slug]);
        Storage::disk('local')->assertExists('media/test-image.webp');
    }

    public function test_publication_requires_permissions_and_admin_media_check(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('publications.store'), $this->payload(['permission_confirmed' => 0]))->assertSessionHasErrors('permission_confirmed');
        $post = Publication::factory()->create(['status' => 'pending', 'review_status' => 'pending']);
        $this->post(route('publications.review', $post), ['action' => 'publish'])->assertSessionHasErrors('media_checked');
        $this->assertSame('pending', $post->fresh()->status);
    }

    public function test_public_text_is_escaped(): void
    {
        $post = Publication::factory()->create(['content' => $this->payload(['title' => '<script>alert(1)</script>', 'media' => []])]);
        $post->update(['status' => 'published', 'published_content' => $post->content]);
        $this->get(route('story', $post->slug))->assertOk()->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_admin_can_save_school_identity_and_logo(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->put(route('settings.update'), [
            'name' => 'SD Contoh',
            'npsn' => '12345678',
            'tagline' => 'Belajar bersama',
            'address' => 'Jalan Pendidikan 1',
            'email' => 'sekolah@example.sch.id',
            'phone' => '+62 812-3456-7890',
            'logo' => UploadedFile::fake()->image('logo.png', 500, 500),
        ])->assertSessionHasNoErrors();
        $this->get(route('profile'))->assertOk()
            ->assertSee('SD Contoh')
            ->assertSee('NPSN 12345678')
            ->assertSee('sekolah@example.sch.id')
            ->assertSee('+62 812-3456-7890');
        $this->get(route('school.logo'))->assertOk();
        $this->assertFalse(SchoolSetting::values()['demo']);
    }

    public function test_school_identity_rejects_invalid_npsn_email_and_phone(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('settings.update'), [
            'name' => 'SD Contoh',
            'tagline' => 'Belajar bersama',
            'npsn' => '123',
            'email' => 'bukan-email',
            'phone' => 'telepon sekolah',
        ])->assertSessionHasErrors(['npsn', 'email', 'phone']);
    }
}
