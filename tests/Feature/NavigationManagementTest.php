<?php

namespace Tests\Feature;

use App\Models\NavigationItem;
use App\Models\SitePage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NavigationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_menu_remains_visible_and_only_admin_can_manage_it(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('Portofolio')->assertSee('Pojok Baca')->assertSee('Tentang Sekolah');
        $this->get(route('navigation.index'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => 'guru']))->get(route('navigation.index'))->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('navigation.index'))->assertOk();
        $this->get(route('navigation.create'))->assertOk()->assertSee('Jenis tujuan')->assertSee('Berkas unduhan');
        $this->get(route('site-pages.create'))->assertOk()->assertSee('Isi halaman');
    }

    public function test_admin_can_create_external_menu_and_hide_it(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $payload = $this->menuPayload(['label' => 'Perpustakaan Nasional', 'type' => 'external', 'target' => 'https://perpusnas.go.id/', 'open_new_tab' => 1]);
        $this->post(route('navigation.store'), $payload)->assertSessionHasNoErrors()->assertRedirect(route('navigation.index'));
        $item = NavigationItem::where('label', 'Perpustakaan Nasional')->firstOrFail();
        $this->get(route('home'))->assertSee('https://perpusnas.go.id/')->assertSee('Perpustakaan Nasional');
        $this->put(route('navigation.update', $item), array_replace($payload, ['is_visible' => 0]))->assertSessionHasNoErrors();
        $this->get(route('home'))->assertDontSee('Perpustakaan Nasional');
    }

    public function test_custom_page_is_only_public_when_published(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->post(route('site-pages.store'), ['title' => 'Kalender Sekolah', 'slug' => 'kalender-sekolah', 'body' => 'Kegiatan bulan ini.', 'is_published' => 0])->assertSessionHasNoErrors();
        $page = SitePage::firstOrFail();
        $this->post(route('navigation.store'), $this->menuPayload(['label' => 'Kalender', 'type' => 'page', 'site_page_id' => $page->id]))->assertSessionHasNoErrors();
        $this->get(route('home'))->assertDontSee('>Kalender<', false);
        $this->get(route('site-pages.show', $page->slug))->assertNotFound();
        $this->put(route('site-pages.update', $page), ['title' => $page->title, 'slug' => $page->slug, 'body' => '<script>alert(1)</script>', 'is_published' => 1])->assertSessionHasNoErrors();
        $this->get(route('home'))->assertSee('>Kalender<', false);
        $this->get(route('site-pages.show', $page->slug))->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_file_menu_can_be_downloaded_only_while_visible_and_rejects_php(): void
    {
        Storage::fake('local');
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->post(route('navigation.store'), $this->menuPayload(['label' => 'Kalender PDF', 'type' => 'file', 'file' => UploadedFile::fake()->create('kalender.pdf', 20, 'application/pdf')]))->assertSessionHasNoErrors();
        $item = NavigationItem::where('label', 'Kalender PDF')->firstOrFail();
        $this->get(route('navigation.file', $item))->assertOk();
        $this->put(route('navigation.update', $item), $this->menuPayload(['label' => 'Kalender PDF', 'type' => 'file', 'is_visible' => 0]))->assertSessionHasNoErrors();
        $this->get(route('navigation.file', $item))->assertNotFound();
        $this->post(route('navigation.store'), $this->menuPayload(['label' => 'Skrip', 'type' => 'file', 'file' => UploadedFile::fake()->create('test.php', 20, 'application/x-php')]))->assertSessionHasErrors('file');
    }

    public function test_unsafe_url_and_teacher_write_are_rejected(): void
    {
        $payload = $this->menuPayload(['label' => 'Berbahaya', 'type' => 'external', 'target' => 'javascript:alert(1)']);
        $this->actingAs(User::factory()->create(['role' => 'guru']))->post(route('navigation.store'), $payload)->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']))->post(route('navigation.store'), $payload)->assertSessionHasErrors('target');
    }

    public function test_menu_order_and_page_deletion_keep_navigation_consistent(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->post(route('site-pages.store'), ['title' => 'Kegiatan', 'slug' => 'kegiatan', 'body' => 'Agenda.', 'is_published' => 1])->assertSessionHasNoErrors();
        $page = SitePage::firstOrFail();
        $this->post(route('navigation.store'), $this->menuPayload(['label' => 'Kegiatan', 'type' => 'page', 'site_page_id' => $page->id, 'sort_order' => 1]))->assertSessionHasNoErrors();
        $item = NavigationItem::where('label', 'Kegiatan')->firstOrFail();
        $html = $this->get(route('home'))->getContent();
        $this->assertLessThan(strpos($html, '>Portofolio<'), strpos($html, '>Kegiatan<'));
        $this->delete(route('site-pages.destroy', $page))->assertRedirect();
        preg_match('/<nav id="main-nav".*?<\/nav>/s', $this->get(route('home'))->getContent(), $navigation);
        $this->assertStringNotContainsString('>Kegiatan<', $navigation[0]);
        $this->assertFalse($item->fresh()->is_visible);
    }

    private function menuPayload(array $changes = []): array
    {
        return array_replace(['label' => 'Menu baru', 'type' => 'internal', 'target' => 'home', 'sort_order' => 40, 'is_visible' => 1, 'open_new_tab' => 0], $changes);
    }
}
