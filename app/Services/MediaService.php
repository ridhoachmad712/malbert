<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MediaService
{
    public function drive(string $url, string $field = 'media'): array
    {
        $parts = parse_url(trim($url));
        if (! $parts || ($parts['scheme'] ?? '') !== 'https' || ($parts['host'] ?? '') !== 'drive.google.com' || isset($parts['user']) || isset($parts['port'])) {
            throw ValidationException::withMessages([$field => 'Gunakan tautan HTTPS file dari drive.google.com.']);
        }
        parse_str($parts['query'] ?? '', $query);
        preg_match('~^/file/d/([a-zA-Z0-9_-]+)(?:/|$)~', $parts['path'] ?? '', $matches);
        $id = $matches[1] ?? (in_array($parts['path'] ?? '', ['/open', '/uc']) ? ($query['id'] ?? '') : '');
        if (! is_string($id) || ! preg_match('/^[a-zA-Z0-9_-]{10,200}$/', $id)) {
            throw ValidationException::withMessages([$field => 'Tautan harus menuju satu file Google Drive, bukan folder.']);
        }
        $key = $query['resourcekey'] ?? null;
        $suffix = is_string($key) && preg_match('/^[a-zA-Z0-9_-]{1,200}$/', $key) ? '?resourcekey='.rawurlencode($key) : '';

        return ['provider' => 'drive', 'id' => $id, 'url' => "https://drive.google.com/file/d/{$id}/view{$suffix}", 'embed' => "https://drive.google.com/file/d/{$id}/preview{$suffix}"];
    }

    public function storeImage(UploadedFile $file, int $maxWidth = 1920): string
    {
        $size = getimagesize($file->getRealPath());
        if (! $size || $size[0] * $size[1] > 24000000) {
            throw ValidationException::withMessages(['cover' => 'Gambar terlalu besar. Gunakan gambar maksimal 24 megapiksel.']);
        }
        $source = imagecreatefromstring(file_get_contents($file->getRealPath()));
        if (! $source) {
            throw ValidationException::withMessages(['cover' => 'Gambar tidak dapat dibaca.']);
        }
        if ($file->getMimeType() === 'image/jpeg' && function_exists('exif_read_data')) {
            $orientation = @exif_read_data($file->getRealPath())['Orientation'] ?? 1;
            if (in_array($orientation, [2, 4, 5, 7])) {
                imageflip($source, IMG_FLIP_HORIZONTAL);
            }
            $angle = match ($orientation) {
                3, 4 => 180, 5, 6 => -90, 7, 8 => 90, default => 0
            };
            if ($angle) {
                $source = imagerotate($source, $angle, 0);
            }
        }
        $ratio = min(1, $maxWidth / max(imagesx($source), imagesy($source)));
        $image = imagescale($source, max(1, (int) (imagesx($source) * $ratio)), max(1, (int) (imagesy($source) * $ratio)));
        ob_start();
        imagewebp($image, null, 80);
        $bytes = ob_get_clean();
        $path = 'media/'.Str::uuid().'.webp';
        if (! Storage::disk('local')->put($path, $bytes)) {
            throw ValidationException::withMessages(['cover' => 'Gambar gagal disimpan. Silakan coba lagi.']);
        }

        return $path;
    }
}
