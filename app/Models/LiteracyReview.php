<?php

namespace App\Models;

use Database\Factories\LiteracyReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiteracyReview extends Model
{
    /** @use HasFactory<LiteracyReviewFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['snapshot' => 'array'];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(LiteracySubmission::class, 'literacy_submission_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
