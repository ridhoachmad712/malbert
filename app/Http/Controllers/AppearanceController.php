<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SchoolSetting;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AppearanceController extends Controller
{
    /**
     * @var array<string, int>
     */
    private const IMAGE_WIDTHS = [
        'hero_image' => 1920,
        'login_image' => 1600,
        'profile_image' => 1600,
        'favicon' => 256,
    ];

    public function edit(Request $request): View
    {
        abort_unless($request->user()->role === 'admin', 403);

        return view('manage.appearance');
    }

    public function update(Request $request, MediaService $mediaService): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate([
            'app_name' => 'required|string|max:50',
            'app_description' => 'required|string|max:160',
            'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'heading_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'text_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'muted_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'secondary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'accent_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'background_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'hero_title' => 'required|string|max:80',
            'hero_highlight' => 'required|string|max:80',
            'hero_description' => 'required|string|max:320',
            'hero_button_label' => 'required|string|max:40',
            'hero_footnote' => 'required|string|max:120',
            'login_label' => 'required|string|max:80',
            'login_title' => 'required|string|max:120',
            'login_description' => 'required|string|max:320',
            'footer_tagline' => 'required|string|max:160',
            'hero_image_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240|dimensions:max_width=8000,max_height=8000',
            'login_image_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240|dimensions:max_width=8000,max_height=8000',
            'profile_image_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240|dimensions:max_width=8000,max_height=8000',
            'favicon_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048|dimensions:max_width=2048,max_height=2048',
            'remove_hero_image' => 'nullable|boolean',
            'remove_login_image' => 'nullable|boolean',
            'remove_profile_image' => 'nullable|boolean',
            'remove_favicon' => 'nullable|boolean',
        ]);
        $this->ensureReadableColors($data);

        $current = SchoolSetting::appearance();
        $pathsToDelete = [];
        foreach (self::IMAGE_WIDTHS as $key => $maxWidth) {
            $uploadKey = $key.'_upload';
            $removeKey = 'remove_'.$key;
            if ($request->hasFile($uploadKey)) {
                $data[$key] = $mediaService->storeImage($request->file($uploadKey), $maxWidth, $uploadKey);
                if ($current[$key]) {
                    $pathsToDelete[] = $current[$key];
                }
            } elseif ($request->boolean($removeKey)) {
                $data[$key] = null;
                if ($current[$key]) {
                    $pathsToDelete[] = $current[$key];
                }
            } else {
                $data[$key] = $current[$key];
            }
            unset($data[$uploadKey], $data[$removeKey]);
        }

        $setting = SchoolSetting::firstOrNew(['id' => 1]);
        $schoolData = $setting->data ?? [];
        $schoolData['appearance'] = array_replace(SchoolSetting::appearanceDefaults(), $data);
        $setting->data = $schoolData;
        $setting->save();
        Storage::disk('local')->delete(array_values(array_unique($pathsToDelete)));
        ActivityLog::record('Memperbarui tampilan dan konten', $data['app_name']);

        return back()->with('success', 'Tampilan dan konten publik berhasil diperbarui.');
    }

    public function reset(Request $request): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $setting = SchoolSetting::firstOrNew(['id' => 1]);
        $schoolData = $setting->data ?? [];
        $current = SchoolSetting::appearance();
        $schoolData['appearance'] = SchoolSetting::appearanceDefaults();
        $setting->data = $schoolData;
        $setting->save();
        Storage::disk('local')->delete(array_filter(array_intersect_key($current, self::IMAGE_WIDTHS)));
        ActivityLog::record('Mengembalikan tampilan bawaan', 'AksiKita');

        return back()->with('success', 'Tampilan dan konten dikembalikan ke bawaan AksiKita.');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function ensureReadableColors(array $data): void
    {
        $background = $data['background_color'];
        foreach (['heading_color', 'text_color', 'muted_color', 'secondary_color'] as $field) {
            if ($this->contrastRatio($data[$field], $background) < 4.5) {
                throw ValidationException::withMessages([$field => 'Pilih warna yang lebih kontras terhadap warna latar.']);
            }
        }
        if ($this->contrastRatio($data['primary_color'], '#ffffff') < 4.5) {
            throw ValidationException::withMessages(['primary_color' => 'Warna utama harus cukup gelap agar teks putih tetap terbaca.']);
        }
    }

    private function contrastRatio(string $foreground, string $background): float
    {
        $luminance = static function (string $color): float {
            $channels = array_map(
                static fn (string $hex): float => hexdec($hex) / 255,
                str_split(ltrim($color, '#'), 2),
            );
            $channels = array_map(
                static fn (float $channel): float => $channel <= 0.03928 ? $channel / 12.92 : (($channel + 0.055) / 1.055) ** 2.4,
                $channels,
            );

            return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
        };
        $first = $luminance($foreground);
        $second = $luminance($background);

        return (max($first, $second) + 0.05) / (min($first, $second) + 0.05);
    }
}
