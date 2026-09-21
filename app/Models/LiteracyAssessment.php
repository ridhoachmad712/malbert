<?php

namespace App\Models;

use Database\Factories\LiteracyAssessmentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiteracyAssessment extends Model
{
    /** @use HasFactory<LiteracyAssessmentFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['score' => 'decimal:2', 'display_score' => 'decimal:1', 'assessed_at' => 'datetime'];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(LiteracySubmission::class, 'literacy_submission_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(LiteracyAssessmentRevision::class)->latest('version');
    }

    public function scopeVisibleTo(Builder $query, User $user): void
    {
        $query->whereHas('classroom', fn (Builder $classrooms) => $classrooms->visibleTo($user));
    }

    /** @return array<string, mixed> */
    public function snapshot(): array
    {
        return $this->only([
            'focus_score', 'engagement_score', 'answer_score', 'retell_score', 'main_idea_score',
            'summary_work_score', 'total_score', 'score', 'display_score', 'category', 'feedback', 'version',
        ]);
    }
}
