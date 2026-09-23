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
}
