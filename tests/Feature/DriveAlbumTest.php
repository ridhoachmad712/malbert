<?php

namespace Tests\Feature;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DriveAlbumTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_replace(['title' => 'Album kegiatan', 'type' => 'kegiatan', 'summary' => 'Belajar bersama', 'body' => 'Cerita kegiatan siswa.', 'event_date' => '2026-09-19', 'school_year' => '2026/2027', 'intent' => 'submit', 'permission_confirmed' => 1, 'drive_folder' => 'https://drive.google.com/drive/folders/Folder123456789?resourcekey=0-folderkey'], $overrides);
    }

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        config(['services.drive.api_key' => 'test-server-key']);
    }

    public function test_teacher_imports_paginated_album_and_publication_keeps_reviewed_snapshot(): void
    {
        Storage::fake('local');
        Http::fakeSequence()->push(['files' => [['id' => 'Photo123456789', 'name' => '01 Foto.jpeg', 'mimeType' => 'image/jpeg', 'resourceKey' => '0-photokey']], 'nextPageToken' => 'next'])->push(['files' => [['id' => 'Video123456789', 'name' => '02 Video.mp4', 'mimeType' => 'video/mp4']]])->push(['files' => [['id' => 'NewPhoto123456', 'name' => 'Foto baru', 'mimeType' => 'image/png']]]);
        $teacher = User::factory()->create();
        $this->actingAs($teacher)->post(route('publications.store'), $this->payload())->assertSessionHasNoErrors()->assertRedirect();
        $post = Publication::firstOrFail();
        $this->assertCount(2, $post->content['album']['media']);
        $this->assertSame('video', $post->content['album']['media'][1]['kind']);
        $this->assertStringContainsString('resourcekey=0-photokey', $post->content['album']['media'][0]['embed']);
        $this->assertSame([], Storage::disk('local')->allFiles());
        Http::assertSentCount(2);
        Http::assertSent(fn ($request) => $request->hasHeader('X-Goog-Api-Key', 'test-server-key') && $request->hasHeader('X-Goog-Drive-Resource-Keys', 'Folder123456789/0-folderkey') && ($request['pageToken'] ?? null) === 'next');
        $this->get(route('publications.preview', $post))->assertOk()->assertSee('data-album', false)->assertSee('02 Video.mp4');
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('publications.review', $post), ['action' => 'publish', 'media_checked' => 1])->assertSessionHasNoErrors();

        $this->actingAs($teacher)->put(route('publications.update', $post), $this->payload())->assertSessionHasNoErrors();
        $this->get(route('story', $post->slug))->assertSee('02 Video.mp4')->assertDontSee('Foto baru');
        $this->get(route('publications.preview', $post))->assertSee('Foto baru');
    }

    public function test_invalid_sources_are_rejected_without_outbound_requests(): void
    {
        Http::fake();
        $this->actingAs(User::factory()->create());
        foreach (['http://127.0.0.1/private', 'https://drive.google.com.evil.test/drive/folders/Folder123456789', 'https://drive.google.com@evil.test/drive/folders/Folder123456789', 'https://drive.google.com:443/drive/folders/Folder123456789', 'https://drive.google.com/file/d/Photo123456789/view'] as $url) {
            $this->post(route('publications.store'), $this->payload(['drive_folder' => $url]))->assertSessionHasErrors('drive_folder');
        }
        Http::assertNothingSent();
        $this->assertDatabaseCount('publications', 0);
    }

    public function test_failed_import_preserves_existing_album_and_content(): void
    {
        $post = Publication::factory()->create();
        $original = $post->content;
        $this->actingAs($post->author);
        foreach ([Http::response([], 403), Http::response(['files' => []]), Http::response(['files' => [], 'incompleteSearch' => true]), Http::response(['unexpected' => true]), Http::failedConnection()] as $response) {
            Http::fake(['*' => $response]);
            $this->put(route('publications.update', $post), $this->payload())->assertSessionHasErrors('drive_folder');
            $this->assertSame($original, $post->fresh()->content);
        }
    }

    public function test_missing_key_is_actionable_and_album_can_be_removed_without_connection(): void
    {
        config(['services.drive.api_key' => null]);
        Http::fake();
        $post = Publication::factory()->create();
        $this->actingAs($post->author)->put(route('publications.update', $post), $this->payload())->assertSessionHasErrors('drive_folder');
        $this->put(route('publications.update', $post), $this->payload(['drive_folder' => '']))->assertSessionHasNoErrors();
        $this->assertNull($post->fresh()->content['album']);
        Http::assertNothingSent();
    }

    public function test_unauthorized_teacher_cannot_trigger_folder_import(): void
    {
        Http::fake();
        $post = Publication::factory()->create();
        $this->actingAs(User::factory()->create())->put(route('publications.update', $post), $this->payload())->assertForbidden();
        Http::assertNothingSent();
    }

    public function test_large_and_cyclic_lists_fail_without_saving_partial_albums(): void
    {
        $this->actingAs(User::factory()->create());
        $files = array_map(fn ($index) => ['id' => 'Photo123456789'.$index, 'name' => 'Photo '.$index, 'mimeType' => 'image/jpeg'], range(1, 201));
        Http::fakeSequence()->push(['files' => $files])->push(['files' => [$files[0]], 'nextPageToken' => 'same'])->push(['files' => [$files[0]], 'nextPageToken' => 'same']);
        $this->post(route('publications.store'), $this->payload())->assertSessionHasErrors('drive_folder');

        $this->post(route('publications.store'), $this->payload())->assertSessionHasErrors('drive_folder');
        $this->assertDatabaseCount('publications', 0);
    }
}
