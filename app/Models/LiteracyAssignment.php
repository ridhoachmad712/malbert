<?php

namespace App\Models;

use Database\Factories\LiteracyAssignmentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiteracyAssignment extends Model
{
    /** @use HasFactory<LiteracyAssignmentFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['assigned_on' => 'date', 'due_at' => 'datetime'];
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function readingResource(): BelongsTo
    {
        return $this->belongsTo(ReadingResource::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(LiteracySubmission::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): void
    {
        $query->whereHas('classroom', fn (Builder $classrooms) => $classrooms->visibleTo($user));
    }
}
