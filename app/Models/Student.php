<?php

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'verified_at' => 'datetime'];
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class)->orderByDesc('starts_on')->orderByDesc('id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function literacySubmissions(): HasMany
    {
        return $this->hasMany(LiteracySubmission::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): void
    {
        $query->whereHas('classroom', fn (Builder $classrooms) => $classrooms->visibleTo($user));
    }
}
