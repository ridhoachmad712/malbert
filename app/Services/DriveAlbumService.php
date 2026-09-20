<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class DriveAlbumService
{
    /** @return array{url: string, media: array, synced_at: string} */
    public function import(string $url): array
    {
        $parts = parse_url(trim($url));
        if (! $parts || ($parts['scheme'] ?? '') !== 'https' || ($parts['host'] ?? '') !== 'drive.google.com' || isset($parts['user']) || isset($parts['port']) || ! preg_match('~^/drive/(?:u/\d+/)?folders/([\w-]{10,200})/?$~', $parts['path'] ?? '', $matches)) {
            $this->fail('Gunakan tautan folder https://drive.google.com/drive/folders/…');
        }
        $id = $matches[1];
        parse_str($parts['query'] ?? '', $query);
        $key = $query['resourcekey'] ?? '';
        $key = is_string($key) && preg_match('/^[\w-]{1,200}$/', $key) ? $key : '';
        $apiKey = config('services.drive.api_key');
        if (! $apiKey) {
            $this->fail('Album Drive belum diaktifkan. Pengelola server perlu mengisi GOOGLE_DRIVE_API_KEY terlebih dahulu.');
        }
        $media = [];
        $pageToken = null;
        $seenTokens = [];
        $client = Http::acceptJson()->connectTimeout(5)->timeout(15)->withOptions(['allow_redirects' => false])->withHeaders(['X-Goog-Api-Key' => $apiKey]);
        if ($key !== '') {
            $client = $client->withHeaders(['X-Goog-Drive-Resource-Keys' => $id.'/'.$key]);
        }
        do {
            try {
                $response = $client->get('https://www.googleapis.com/drive/v3/files', array_filter([
                    'q' => "'{$id}' in parents and trashed = false and (mimeType contains 'image/' or mimeType contains 'video/')",
                    'fields' => 'nextPageToken,incompleteSearch,files(id,name,mimeType,resourceKey)',
                    'orderBy' => 'name_natural', 'pageSize' => 100, 'pageToken' => $pageToken,
                ], fn ($value) => $value !== null));
            } catch (ConnectionException) {
                $this->fail('Google Drive belum dapat dihubungi. Coba simpan kembali beberapa saat lagi.');
            }
            if (! $response->successful()) {
                $this->fail('Folder belum dapat dibaca. Pastikan akses folder dan isinya “Siapa saja yang memiliki link”, serta koneksi Drive sudah aktif.');
            }
            if (! is_array($response->json('files')) || $response->json('incompleteSearch', false)) {
                $this->fail('Daftar media belum lengkap. Coba lagi; album lama tetap tersimpan.');
            }
            foreach ($response->json('files') as $file) {
                $mime = $file['mimeType'] ?? '';
                if (! is_string($mime) || (! str_starts_with($mime, 'image/') && ! str_starts_with($mime, 'video/'))) {
                    continue;
                }
                if (! is_string($file['id'] ?? null) || ! preg_match('/^[\w-]{10,200}$/', $file['id'])) {
                    $this->fail('Data media Google Drive tidak valid. Coba lagi.');
                }
                $resourceKey = $file['resourceKey'] ?? '';
                $suffix = is_string($resourceKey) && preg_match('/^[\w-]{1,200}$/', $resourceKey) ? '?resourcekey='.rawurlencode($resourceKey) : '';
                $media[$file['id']] = [...app(MediaService::class)->drive('https://drive.google.com/file/d/'.$file['id'].'/view'.$suffix), 'kind' => str_starts_with($mime, 'video/') ? 'video' : 'photo', 'caption' => mb_substr((string) ($file['name'] ?? 'Dokumentasi'), 0, 300)];
                if (count($media) > 200) {
                    $this->fail('Maksimal 200 foto/video per album. Pisahkan folder menjadi beberapa cerita.');
                }
            }
            $pageToken = $response->json('nextPageToken');
            if ($pageToken !== null && (! is_string($pageToken) || isset($seenTokens[$pageToken]) || count($seenTokens) >= 9)) {
                $this->fail('Daftar media terlalu panjang atau belum lengkap. Pisahkan folder menjadi beberapa cerita.');
            }
            if ($pageToken) {
                $seenTokens[$pageToken] = true;
            }
        } while ($pageToken);
        if ($media === []) {
            $this->fail('Tidak ditemukan foto/video yang dapat diakses langsung di folder ini. Isi subfolder tidak disertakan.');
        }

        return ['url' => 'https://drive.google.com/drive/folders/'.$id.($key !== '' ? '?resourcekey='.rawurlencode($key) : ''), 'media' => array_values($media), 'synced_at' => now()->toIso8601String()];
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['drive_folder' => $message]);
    }
}
