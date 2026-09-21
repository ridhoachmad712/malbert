<?php

namespace App\Models;

use Database\Factories\PointTransactionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointTransaction extends Model
{
    /** @use HasFactory<PointTransactionFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['occurred_on' => 'date', 'snapshot' => 'array'];
    }

    /** @return array<string, array{label: string, points: int}> */
    public static function activities(): array
    {
        return [
            'read_15' => ['label' => 'Membaca 15 menit', 'points' => 5],
            'retell' => ['label' => 'Membaca dan menceritakan kembali', 'points' => 10],
            'summary' => ['label' => 'Membuat ringkasan', 'points' => 10],
            'creative_work' => ['label' => 'Membuat karya/tulisan kreatif', 'points' => 15],
            'discipline' => ['label' => 'Disiplin dan bertanggung jawab', 'points' => 5],
            'cooperation' => ['label' => 'Membantu teman/bekerja sama', 'points' => 5],
            'permata' => ['label' => 'Menerapkan PERMATA', 'points' => 5],
            'reflection_after_incident' => ['label' => 'Menyelesaikan refleksi setelah pelanggaran', 'points' => 10],
        ];
    }

    public static function appreciation(int $points): ?string
    {
        return match (true) {
            $points >= 100 => 'Duta TEBAK AKSI',
            $points >= 80 => 'Kreator TEBAK AKSI',
            $points >= 60 => 'Pejuang Karakter',
            $points >= 40 => 'Sahabat Baca',
            $points >= 20 => 'Bintang Literasi',
            default => null,
        };
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

    public function submission(): BelongsTo
    {
        return $this->belongsTo(LiteracySubmission::class, 'literacy_submission_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function adjustedTransaction(): BelongsTo
    {
        return $this->belongsTo(self::class, 'adjusts_transaction_id');
    }

    public function corrections(): HasMany
    {
        return $this->hasMany(self::class, 'adjusts_transaction_id')->oldest('id');
    }

    public function scopeVisibleTo(Builder $query, User $user): void
    {
        $query->whereHas('classroom', fn (Builder $classrooms) => $classrooms->visibleTo($user));
    }
}
