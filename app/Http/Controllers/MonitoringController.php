<?php

namespace App\Http\Controllers;

use App\Models\AssessmentSetting;
use App\Models\Classroom;
use App\Models\Student;
use App\Services\MonitoringReport;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MonitoringController extends Controller
{
    public function index(Request $request, MonitoringReport $reports): View
    {
        $request->validate(['classroom' => ['nullable', 'integer'], 'date' => ['nullable', 'date_format:Y-m-d']]);
        [$classrooms, $classroom] = $this->classroomSelection($request);
        $setting = AssessmentSetting::current();
        $reference = $classroom ? $this->referenceDate($request, $classroom) : CarbonImmutable::now();
        $report = null;

        if ($classroom && $setting?->monitoringConfigured()) {
            $report = $reports->classroom($classroom, $setting, $reports->configuredPeriod($setting, $classroom->academicYear, $reference));
        }

        return view('manage.monitoring.index', compact('classrooms', 'classroom', 'setting', 'reference', 'report'));
    }

    public function student(Request $request, Classroom $classroom, Student $student, MonitoringReport $reports): View
    {
        $classroom = Classroom::visibleTo($request->user())->with('academicYear')->findOrFail($classroom->id);
        $this->ensureStudentBelongsToClassroom($student, $classroom);
        $setting = AssessmentSetting::current();
        abort_unless($setting?->monitoringConfigured(), 409, 'Admin harus melengkapi aturan monitoring terlebih dahulu.');

        return view('manage.monitoring.student', [
            'classroom' => $classroom,
            'student' => $student,
            'setting' => $setting,
            'progress' => $reports->studentProgress($classroom, $student, $setting),
        ]);
    }

    public function monthly(Request $request, MonitoringReport $reports): View
    {
        return view('manage.monitoring.monthly', $this->monthlyData($request, $reports));
    }

    public function export(Request $request, MonitoringReport $reports): StreamedResponse
    {
        $data = $this->monthlyData($request, $reports);
        $classroom = $data['classroom'];
        $report = $data['report'];
        $filename = 'laporan-'.Str::slug($classroom->name).'-'.$data['month'].'.csv';

        return response()->streamDownload(function () use ($classroom, $report) {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Laporan bulanan TEBAK AKSI'], ',', '"', '\\', "\r\n");
            fputcsv($output, ['Rombel', $this->safeCsv($classroom->name)], ',', '"', '\\', "\r\n");
            fputcsv($output, ['Periode', $report['period']['label']], ',', '"', '\\', "\r\n");
            fputcsv($output, [], ',', '"', '\\', "\r\n");
            fputcsv($output, ['Nama murid', 'NIS', 'Hasil disetujui', 'Jumlah penilaian', 'Nilai', 'Status nilai', 'Aktivitas poin', 'Total poin', 'Apresiasi'], ',', '"', '\\', "\r\n");
            foreach ($report['rows'] as $row) {
                fputcsv($output, [
                    $this->safeCsv($row['student']->name),
                    $this->safeCsv((string) ($row['student']->nis ?? '')),
                    $row['submission_count'],
                    $row['assessment_count'],
                    $row['score'] ?? 'Belum dinilai',
                    $row['successful'] === null ? 'Belum dinilai' : ($row['successful'] ? 'Mencapai ambang' : 'Perlu tindak lanjut'),
                    $row['activity_count'],
                    $row['points'],
                    $row['appreciation'] ?? 'Belum ada tingkat',
                ], ',', '"', '\\', "\r\n");
            }
            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return array{0: Collection<int, Classroom>, 1: ?Classroom} */
    private function classroomSelection(Request $request): array
    {
        $classrooms = Classroom::visibleTo($request->user())
            ->with('academicYear')
            ->orderByDesc('academic_year_id')->orderBy('grade')->orderBy('name')->get();
        $classroom = $request->filled('classroom')
            ? Classroom::visibleTo($request->user())->with('academicYear')->findOrFail($request->integer('classroom'))
            : $classrooms->first();

        return [$classrooms, $classroom];
    }

    private function referenceDate(Request $request, Classroom $classroom): CarbonImmutable
    {
        $start = CarbonImmutable::instance($classroom->academicYear->starts_on);
        $end = CarbonImmutable::instance($classroom->academicYear->ends_on);
        if ($request->filled('date')) {
            $date = CarbonImmutable::createFromFormat('Y-m-d', $request->string('date')->toString())->startOfDay();
            if ($date->lt($start) || $date->gt($end)) {
                throw ValidationException::withMessages(['date' => 'Tanggal acuan harus berada dalam tahun ajaran rombel.']);
            }

            return $date;
        }

        return CarbonImmutable::now()->max($start)->min($end);
    }

    /** @return array<string, mixed> */
    private function monthlyData(Request $request, MonitoringReport $reports): array
    {
        $request->validate(['classroom' => ['nullable', 'integer'], 'month' => ['nullable', 'date_format:Y-m']]);
        [$classrooms, $classroom] = $this->classroomSelection($request);
        $setting = AssessmentSetting::current();
        abort_unless($setting?->monitoringConfigured(), 409, 'Admin harus melengkapi aturan monitoring terlebih dahulu.');
        abort_unless($classroom, 409, 'Belum ada rombel yang dapat dilaporkan.');
        $defaultReference = CarbonImmutable::now()
            ->max(CarbonImmutable::instance($classroom->academicYear->starts_on))
            ->min(CarbonImmutable::instance($classroom->academicYear->ends_on));
        $month = $request->input('month', $defaultReference->format('Y-m'));
        $reference = CarbonImmutable::createFromFormat('Y-m-d', $month.'-01')->startOfDay();
        $yearStart = CarbonImmutable::instance($classroom->academicYear->starts_on)->startOfMonth();
        $yearEnd = CarbonImmutable::instance($classroom->academicYear->ends_on)->endOfMonth();
        if ($reference->lt($yearStart) || $reference->gt($yearEnd)) {
            throw ValidationException::withMessages(['month' => 'Bulan laporan harus berada dalam tahun ajaran rombel.']);
        }
        $period = $reports->monthlyPeriod($classroom->academicYear, $reference);

        return [
            'classrooms' => $classrooms,
            'classroom' => $classroom,
            'setting' => $setting,
            'month' => $month,
            'report' => $reports->classroom($classroom, $setting, $period),
        ];
    }

    private function ensureStudentBelongsToClassroom(Student $student, Classroom $classroom): void
    {
        $belongs = $student->classroom_id === $classroom->id
            || $student->enrollments()->where('classroom_id', $classroom->id)->exists()
            || $student->literacyAssessments()->where('classroom_id', $classroom->id)->exists()
            || $student->pointTransactions()->where('classroom_id', $classroom->id)->exists();
        abort_unless($belongs, 404);
    }

    private function safeCsv(string $value): string
    {
        return preg_match('/^[=+\-@\t\r]/u', $value) ? "'".$value : $value;
    }
}
