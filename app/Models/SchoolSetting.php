<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['data' => 'array'];
    }

    public static function values(): array
    {
        return array_replace(['name' => 'Sekolah Kita', 'npsn' => '', 'tagline' => 'Tempat tumbuh, berkarya, dan bercerita.', 'description' => '', 'vision' => '', 'mission' => '', 'address' => '', 'email' => '', 'phone' => '', 'contact' => '', 'demo' => false], static::first()?->data ?? []);
    }

    /**
     * @return array<string, string|null>
     */
    public static function appearanceDefaults(): array
    {
        return [
            'app_name' => 'AksiKita',
            'app_description' => 'Portal Literasi, Karya, dan Karakter Siswa',
            'primary_color' => '#2759e5',
            'heading_color' => '#172d4a',
            'text_color' => '#21344c',
            'muted_color' => '#596a7d',
            'secondary_color' => '#24765d',
            'accent_color' => '#ffda67',
            'background_color' => '#ffffff',
            'hero_title' => 'Langkah kecil.',
            'hero_highlight' => 'Karya luar biasa.',
            'hero_description' => 'Intip keseruan belajar, temukan karya penuh imajinasi, dan rayakan pencapaian teman-teman kita.',
            'hero_button_label' => 'Jelajahi cerita',
            'hero_footnote' => 'Baca, berkarya, dan bertumbuh bersama.',
            'login_label' => 'RUANG GURU & ADMIN',
            'login_title' => 'Baca, berkarya, bertumbuh bersama.',
            'login_description' => 'Kelola kegiatan literasi, karya, perkembangan karakter, dan pencapaian siswa dalam satu portal sekolah.',
            'footer_tagline' => 'Baca, berkarya, dan bertumbuh bersama.',
            'hero_image' => null,
            'login_image' => null,
            'profile_image' => null,
            'favicon' => null,
        ];
    }

    /**
     * @return array<string, string|null>
     */
    public static function appearance(): array
    {
        $appearance = static::first()?->data['appearance'] ?? [];

        return array_replace(static::appearanceDefaults(), is_array($appearance) ? $appearance : []);
    }
}
