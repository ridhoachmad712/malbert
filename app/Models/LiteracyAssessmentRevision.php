<?php

namespace App\Models;

use Database\Factories\LiteracyAssessmentRevisionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiteracyAssessmentRevision extends Model
{
    /** @use HasFactory<LiteracyAssessmentRevisionFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['snapshot' => 'array'];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(LiteracyAssessment::class, 'literacy_assessment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
