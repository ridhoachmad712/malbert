<?php

namespace App\Models;

use Database\Factories\NavigationItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NavigationItem extends Model
{
    /** @use HasFactory<NavigationItemFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_visible' => 'boolean', 'open_new_tab' => 'boolean'];
    }

    public function sitePage(): BelongsTo
    {
        return $this->belongsTo(SitePage::class);
    }

    public function url(): ?string
    {
        return match ($this->type) {
            'internal' => match ($this->target) {
                'portfolio' => route('home').'#jelajah',
                'home' => route('home'),
                'readings.public' => route('readings.public'),
                'profile' => route('profile'),
                default => null,
            },
            'page' => $this->sitePage?->is_published ? route('site-pages.show', $this->sitePage->slug) : null,
            'file' => $this->file_path ? route('navigation.file', $this) : null,
            'external' => filter_var($this->target, FILTER_VALIDATE_URL) && in_array(parse_url($this->target, PHP_URL_SCHEME), ['http', 'https'], true) ? $this->target : null,
            default => null,
        };
    }

    public function isActive(): bool
    {
        return match ($this->type) {
            'internal' => match ($this->target) {
                'portfolio', 'home' => request()->routeIs('home', 'story'),
                'readings.public' => request()->routeIs('readings.public'),
                'profile' => request()->routeIs('profile'),
                default => false,
            },
            'page' => request()->routeIs('site-pages.show') && request()->route('slug') === $this->sitePage?->slug,
            default => false,
        };
    }
}
