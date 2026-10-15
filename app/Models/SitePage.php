<?php

namespace App\Models;

use Database\Factories\SitePageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SitePage extends Model
{
    /** @use HasFactory<SitePageFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function navigationItems(): HasMany
    {
        return $this->hasMany(NavigationItem::class);
    }
}
