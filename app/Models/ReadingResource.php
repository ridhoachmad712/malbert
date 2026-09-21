<?php

namespace App\Models;

use Database\Factories\ReadingResourceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReadingResource extends Model
{
    /** @use HasFactory<ReadingResourceFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_public' => 'boolean', 'published_at' => 'datetime'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(LiteracyAssignment::class);
    }

    public function canEdit(User $user): bool
    {
        return $user->role === 'admin' || $this->user_id === $user->id;
    }
}
