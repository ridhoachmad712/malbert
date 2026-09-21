<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\AssessmentSetting;
use App\Models\Classroom;
use App\Models\LiteracyAssessment;
use App\Models\LiteracySubmission;
use App\Models\PointTransaction;
use App\Models\Student;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MonitoringReport
{
    /** @return array{start: CarbonImmutable, end: CarbonImmutable, label: string} */
    public function configuredPeriod(AssessmentSetting $setting, AcademicYear $year, CarbonInterface $reference): array
    {
        $referenceDate = CarbonImmutable::instance($reference);
        $yearStart = CarbonImmutable::instance($year->starts_on)->startOfDay();
        $yearEnd = CarbonImmutable::instance($year->ends_on)->endOfDay();

        if ($setting->monitoring_period === 'academic_year') {
            return ['start' => $yearStart, 'end' => $yearEnd, 'label' => 'Tahun ajaran '.$year->name];
        }

        if ($setting->monitoring_period === 'semester') {
            $secondSemesterStart = $yearStart->addMonths(6);
            $isFirstSemester = $referenceDate->lt($secondSemesterStart);

            return [
                'start' => $isFirstSemester ? $yearStart : $secondSemesterStart,
                'end' => $isFirstSemester ? $secondSemesterStart->subDay()->endOfDay() : $yearEnd,
                'label' => 'Semester '.($isFirstSemester ? 'Ganjil' : 'Genap').' '.$year->name,
            ];
        }

        return $this->monthlyPeriod($year, $referenceDate);
    }

    /** @return array{start: CarbonImmutable, end: CarbonImmutable, label: string} */
    public function monthlyPeriod(AcademicYear $year, CarbonInterface $reference): array
    {
        $referenceDate = CarbonImmutable::instance($reference);
        $yearStart = CarbonImmutable::instance($year->starts_on)->startOfDay();
        $yearEnd = CarbonImmutable::instance($year->ends_on)->endOfDay();

        return [
            'start' => $referenceDate->startOfMonth()->max($yearStart),
            'end' => $referenceDate->endOfMonth()->min($yearEnd),
            'label' => $referenceDate->translatedFormat('F Y'),
        ];
    }

    /**
     * @param  array{start: CarbonImmutable, end: CarbonImmutable, label: string}  $period
     * @return array<string, mixed>
     */
    public function classroom(Classroom $classroom, AssessmentSetting $setting, array $period): array
    {
        $students = $this->population($classroom, $setting, $period);
        $rows = $this->rows($students, $classroom, $setting, $period);
        $assessedRows = $rows->whereNotNull('score');
        $denominatorRows = $setting->unassessed_rule === 'exclude' ? $assessedRows : $rows;
        $successfulCount = $denominatorRows->where('successful', true)->count();
        $denominator = $denominatorRows->count();
        $achievement = $denominator > 0 ? round(($successfulCount / $denominator) * 100, 1) : null;

        return [
            'period' => $period,
            'rows' => $rows,
            'population_count' => $rows->count(),
            'assessed_count' => $assessedRows->count(),
            'unassessed_count' => $rows->whereNull('score')->count(),
            'submission_count' => $rows->sum('submission_count'),
            'assessment_count' => $rows->sum('assessment_count'),
            'average_score' => $assessedRows->isEmpty() ? null : round((float) $assessedRows->avg('score'), 1),
            'point_total' => (int) $rows->sum('points'),
            'successful_count' => $successfulCount,
            'denominator' => $denominator,
            'achievement_percentage' => $achievement,
            'target_met' => $achievement === null ? null : $achievement >= 80,
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public function studentProgress(Classroom $classroom, Student $student, AssessmentSetting $setting): Collection
    {
        $cursor = CarbonImmutable::instance($classroom->academicYear->starts_on)->startOfMonth();
        $lastMonth = CarbonImmutable::instance($classroom->academicYear->ends_on)->startOfMonth();
        $progress = collect();

        while ($cursor->lte($lastMonth)) {
            $period = $this->monthlyPeriod($classroom->academicYear, $cursor);
            $row = $this->rows(collect([$student]), $classroom, $setting, $period)->first();
            $progress->push([...$row, 'period' => $period]);
            $cursor = $cursor->addMonth();
        }

        return $progress;
    }

    /**
     * @param  array{start: CarbonImmutable, end: CarbonImmutable, label: string}  $period
     * @return Collection<int, Student>
     */
    private function population(Classroom $classroom, AssessmentSetting $setting, array $period): Collection
    {
        $query = Student::query();
        if ($setting->achievement_population === 'participants') {
            $query->whereHas('literacySubmissions', function (Builder $submissions) use ($classroom, $period) {
                $submissions->where('status', 'approved')
                    ->whereBetween('journal_date', [$period['start']->toDateString(), $period['end']->toDateString()])
                    ->whereHas('assignment', fn (Builder $assignments) => $assignments->where('classroom_id', $classroom->id));
            });
        } else {
            $query->where(function (Builder $students) use ($classroom, $period) {
                $students->whereHas('enrollments', function (Builder $enrollments) use ($classroom, $period) {
                    $enrollments->where('classroom_id', $classroom->id)
                        ->whereDate('starts_on', '<=', $period['end']->toDateString())
                        ->where(fn (Builder $dates) => $dates->whereNull('ends_on')->orWhereDate('ends_on', '>=', $period['start']->toDateString()));
                })->orWhere(function (Builder $legacy) use ($classroom) {
                    $legacy->where('classroom_id', $classroom->id)->where('active', true)->whereDoesntHave('enrollments');
                });
            });
        }

        return $query->orderBy('name')->orderBy('id')->get();
    }

    /**
     * @param  Collection<int, Student>  $students
     * @param  array{start: CarbonImmutable, end: CarbonImmutable, label: string}  $period
     * @return Collection<int, array<string, mixed>>
     */
    private function rows(Collection $students, Classroom $classroom, AssessmentSetting $setting, array $period): Collection
    {
        $studentIds = $students->pluck('id');
        if ($studentIds->isEmpty()) {
            return collect();
        }

        $submissions = LiteracySubmission::query()
            ->whereIn('student_id', $studentIds)
            ->where('status', 'approved')
            ->whereBetween('journal_date', [$period['start']->toDateString(), $period['end']->toDateString()])
            ->whereHas('assignment', fn (Builder $assignments) => $assignments->where('classroom_id', $classroom->id))
            ->get()->groupBy('student_id');
        $assessments = LiteracyAssessment::query()
            ->whereIn('student_id', $studentIds)
            ->where('classroom_id', $classroom->id)
            ->with('submission')
            ->whereHas('submission', fn (Builder $submission) => $submission->whereBetween('journal_date', [$period['start']->toDateString(), $period['end']->toDateString()]))
            ->get()->groupBy('student_id');
        $earnedTransactionIds = PointTransaction::query()
            ->whereIn('student_id', $studentIds)
            ->where('classroom_id', $classroom->id)
            ->where('kind', 'earn')
            ->whereBetween('occurred_on', [$period['start']->toDateString(), $period['end']->toDateString()])
            ->pluck('id');
        $transactions = PointTransaction::query()
            ->whereIn('student_id', $studentIds)
            ->where(fn (Builder $query) => $query->whereIn('id', $earnedTransactionIds)->orWhereIn('adjusts_transaction_id', $earnedTransactionIds))
            ->get()->groupBy('student_id');

        return $students->map(function (Student $student) use ($submissions, $assessments, $transactions, $setting) {
            $studentAssessments = $assessments->get($student->id, collect());
            $latest = $studentAssessments->sortByDesc(fn (LiteracyAssessment $assessment) => ($assessment->submission->journal_date?->format('Y-m-d') ?? '').str_pad((string) $assessment->id, 10, '0', STR_PAD_LEFT))->first();
            $score = match (true) {
                $studentAssessments->isEmpty() => null,
                $setting->student_score_method === 'latest' => (float) $latest->display_score,
                default => round((float) $studentAssessments->avg('display_score'), 1),
            };
            $studentTransactions = $transactions->get($student->id, collect());
            $points = (int) $studentTransactions->sum('points');

            return [
                'student' => $student,
                'submission_count' => $submissions->get($student->id, collect())->count(),
                'assessment_count' => $studentAssessments->count(),
                'score' => $score,
                'successful' => $score === null ? null : $score >= (float) $setting->success_threshold,
                'activity_count' => $studentTransactions->where('kind', 'earn')->count(),
                'points' => $points,
                'appreciation' => PointTransaction::appreciation($points),
            ];
        });
    }
}
