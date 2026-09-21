<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\AssessmentSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentSetting extends Model
{
    /** @use HasFactory<AssessmentSettingFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['success_threshold' => 'decimal:2', 'configured_at' => 'datetime'];
    }

    public function configurator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'configured_by');
    }

    public static function current(): ?self
    {
        return static::query()->first();
    }

    public function monitoringConfigured(): bool
    {
        return filled($this->monitoring_period)
            && filled($this->student_score_method)
            && filled($this->achievement_population)
            && filled($this->unassessed_rule);
    }

    public function roundedScore(float $score): float
    {
        return round($score, $this->score_rounding === 'whole' ? 0 : 1);
    }

    public function category(float $displayScore): string
    {
        return match (true) {
            $displayScore >= 86 => 'very_good',
            $displayScore >= 76 => 'good',
            $displayScore >= 60 => 'fair',
            default => 'guidance',
        };
    }

    public function periodKey(CarbonInterface $date, AcademicYear $year, string $semester): string
    {
        return match ($this->point_period) {
            'month' => $date->format('Y-m'),
            'semester' => $year->id.':'.$semester,
            default => (string) $year->id,
        };
    }

    public function periodLabel(CarbonInterface $date, AcademicYear $year, string $semester): string
    {
        return match ($this->point_period) {
            'month' => $date->translatedFormat('F Y'),
            'semester' => 'Semester '.ucfirst($semester).' · '.$year->name,
            default => 'Tahun ajaran '.$year->name,
        };
    }
}
