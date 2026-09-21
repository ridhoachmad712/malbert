<?php

namespace App\Models;

use Database\Factories\LiteracySubmissionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiteracySubmission extends Model
{
    /** @use HasFactory<LiteracySubmissionFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['journal_date' => 'date', 'submitted_at' => 'datetime', 'reviewed_at' => 'datetime'];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(LiteracyAssignment::class, 'literacy_assignment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(LiteracyReview::class)->latest('id');
    }

    public function scopeVisibleTo(Builder $query, User $user): void
    {
        $query->whereHas('assignment', fn (Builder $assignments) => $assignments->visibleTo($user));
    }

    /** @return array<string, mixed> */
    public function snapshot(): array
    {
        return $this->only([
            'journal_date', 'duration_minutes', 'summary', 'work_type', 'work_text', 'work_url',
            'work_path', 'work_original_name', 'reflection_understanding', 'reflection_feeling',
            'reflection_commitment', 'status', 'revision_number', 'submitted_at', 'feedback',
        ]);
    }
}
