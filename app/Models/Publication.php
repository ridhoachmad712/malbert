<?php

namespace App\Models;

use Database\Factories\PublicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Publication extends Model
{
    /** @use HasFactory<PublicationFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['content' => 'array', 'published_content' => 'array', 'source_snapshot' => 'array', 'featured' => 'boolean', 'published_at' => 'datetime'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function literacySubmission(): BelongsTo
    {
        return $this->belongsTo(LiteracySubmission::class);
    }

    public function canEdit(User $user): bool
    {
        return $user->role === 'admin' || $this->user_id === $user->id;
    }

    public function mediaUrl(string $path): string
    {
        return route('media', [$this, basename($path)]);
    }
}
