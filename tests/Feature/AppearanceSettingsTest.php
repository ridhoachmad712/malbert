<?php

namespace Tests\Feature;

use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AppearanceSettingsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_replace([
            'app_name' => 'Pelita Siswa',
            'app_description' => 'Portal belajar dan karya sekolah',
            'primary_color' => '#2147a8',
            'heading_color' => '#15243a',
            'text_color' => '#24364d',
            'muted_color' => '#53657a',
            'secondary_color' => '#25654f',
            'accent_color' => '#ffd45a',
            'background_color' => '#ffffff',
            'hero_title' => 'Belajar bersama.',
            'hero_highlight' => 'Tumbuh penuh makna.',
            'hero_description' => 'Temukan kegiatan dan karya terbaik dari sekolah kami.',
            'hero_button_label' => 'Lihat karya',
            'hero_footnote' => 'Setiap proses layak dirayakan.',
            'login_label' => 'AREA PENGELOLA',
            'login_title' => 'Kelola cerita sekolah.',
            'login_description' => 'Masuk untuk mengelola kegiatan, karya, dan perkembangan siswa.',
            'footer_tagline' => 'Belajar, berkarya, dan bertumbuh.',
        ], $overrides);
    }

    public function test_only_admin_can_manage_appearance(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);

        $this->get(route('appearance.edit'))->assertRedirect(route('login'));
        $this->actingAs($teacher)->get(route('appearance.edit'))->assertForbidden();
        $this->put(route('appearance.update'), $this->payload())->assertForbidden();
        $this->delete(route('appearance.reset'))->assertForbidden();
    }

    public function test_admin_can_customize_public_content_colors_and_images(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin']);
        $payload = $this->payload([
            'hero_image_upload' => UploadedFile::fake()->image('hero.jpg', 1600, 900),
            'login_image_upload' => UploadedFile::fake()->image('login.png', 1200, 900),
            'profile_image_upload' => UploadedFile::fake()->image('profile.jpg', 1200, 900),
            'favicon_upload' => UploadedFile::fake()->image('icon.png', 256, 256),
        ]);

        $this->actingAs($admin)->put(route('appearance.update'), $payload)
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $appearance = SchoolSetting::appearance();
        $this->assertSame('Pelita Siswa', $appearance['app_name']);
        $this->assertSame('#2147a8', $appearance['primary_color']);
        foreach (['hero_image', 'login_image', 'profile_image', 'favicon'] as $key) {
            $this->assertNotNull($appearance[$key]);
            Storage::disk('local')->assertExists($appearance[$key]);
        }
        $this->get(route('home'))->assertOk()
            ->assertSee('Pelita Siswa')
            ->assertSee('Portal belajar dan karya sekolah')
            ->assertSee('Belajar bersama.')
            ->assertSee('Tumbuh penuh makna.')
            ->assertSee('--blue:#2147a8', false);
        foreach (['hero', 'login', 'profile', 'favicon'] as $slot) {
            $this->get(route('appearance.asset', ['slot' => $slot]))->assertOk();
        }
    }

    public function test_unreadable_theme_colors_are_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('appearance.update'), $this->payload([
            'primary_color' => '#ffffff',
            'text_color' => '#eeeeee',
        ]))->assertSessionHasErrors('text_color');

        $this->assertSame('AksiKita', SchoolSetting::appearance()['app_name']);
    }

    public function test_admin_can_restore_default_appearance(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->put(route('appearance.update'), $this->payload([
            'hero_image_upload' => UploadedFile::fake()->image('hero.jpg', 1200, 800),
        ]))->assertSessionHasNoErrors();
        $heroPath = SchoolSetting::appearance()['hero_image'];

        $this->delete(route('appearance.reset'))->assertSessionHas('success');

        $this->assertSame(SchoolSetting::appearanceDefaults(), SchoolSetting::appearance());
        Storage::disk('local')->assertMissing($heroPath);
        $this->get(route('appearance.asset', ['slot' => 'hero']))->assertNotFound();
    }

    public function test_saving_school_profile_preserves_custom_appearance(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->put(route('appearance.update'), $this->payload())->assertSessionHasNoErrors();

        $this->put(route('settings.update'), [
            'name' => 'SD Contoh',
            'tagline' => 'Belajar bersama',
        ])->assertSessionHasNoErrors();

        $this->assertSame('Pelita Siswa', SchoolSetting::appearance()['app_name']);
        $this->assertSame('SD Contoh', SchoolSetting::values()['name']);
    }
}
