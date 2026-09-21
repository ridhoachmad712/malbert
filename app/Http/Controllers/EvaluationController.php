<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveAssessmentSettingRequest;
use App\Http\Requests\SaveLiteracyAssessmentRequest;
use App\Http\Requests\SavePointTransactionRequest;
use App\Models\ActivityLog;
use App\Models\AssessmentSetting;
use App\Models\Classroom;
use App\Models\LiteracyAssessment;
use App\Models\LiteracyAssessmentRevision;
use App\Models\LiteracySubmission;
use App\Models\PointTransaction;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EvaluationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $classrooms = Classroom::visibleTo($user)->with('academicYear')->orderByDesc('academic_year_id')->orderBy('grade')->orderBy('name')->get();
        $classroom = $request->filled('classroom')
            ? Classroom::visibleTo($user)->with('academicYear')->findOrFail($request->integer('classroom'))
            : $classrooms->first();
        $setting = AssessmentSetting::current();
        $periodKey = $classroom && $setting ? $setting->periodKey(now(), $classroom->academicYear, $classroom->academicYear->semester ?? 'ganjil') : null;
        $students = $classroom?->students()->where('active', true)
            ->withCount([
                'literacySubmissions as approved_submissions_count' => fn (Builder $query) => $query->where('status', 'approved'),
                'literacyAssessments',
            ])
            ->withSum(['pointTransactions as period_points' => fn (Builder $query) => $query->where('period_key', $periodKey)], 'points')
            ->orderBy('name')->get() ?? collect();

        return view('manage.evaluation.index', compact('classrooms', 'classroom', 'setting', 'students'));
    }

    public function settings(Request $request): View
    {
        abort_unless($request->user()->role === 'admin', 403);

        return view('manage.evaluation.settings', ['setting' => AssessmentSetting::current() ?? new AssessmentSetting]);
    }

    public function saveSettings(SaveAssessmentSettingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($request, $data) {
            AssessmentSetting::query()->lockForUpdate()->get();
            AssessmentSetting::updateOrCreate(['id' => 1], [
                ...$data,
                'configured_by' => $request->user()->id,
                'configured_at' => now(),
            ]);
            ActivityLog::record('Menetapkan aturan penilaian', 'Pembulatan, ambang keberhasilan, periode poin, dan pengulangan');
        });

        return redirect()->route('evaluation.index')->with('success', 'Aturan penilaian disimpan dan siap digunakan.');
    }

    public function assessment(Request $request, LiteracySubmission $literacySubmission): View
    {
        $setting = AssessmentSetting::current();
        abort_unless($setting, 409, 'Admin harus menetapkan aturan penilaian terlebih dahulu.');
        $literacySubmission = LiteracySubmission::visibleTo($request->user())
            ->with(['student', 'assignment.classroom.academicYear', 'assessment.revisions.user'])
            ->findOrFail($literacySubmission->id);
        abort_unless($literacySubmission->status === 'approved', 409, 'Hasil literasi harus disetujui sebelum dinilai.');

        return view('manage.evaluation.assessment', [
            'submission' => $literacySubmission,
            'assessment' => $literacySubmission->assessment ?? new LiteracyAssessment,
            'setting' => $setting,
        ]);
    }

    public function saveAssessment(SaveLiteracyAssessmentRequest $request, LiteracySubmission $literacySubmission): RedirectResponse
    {
        $setting = AssessmentSetting::current();
        abort_unless($setting, 409, 'Admin harus menetapkan aturan penilaian terlebih dahulu.');
        $literacySubmission = LiteracySubmission::visibleTo($request->user())->with(['assignment.classroom', 'student', 'assessment'])->findOrFail($literacySubmission->id);
        abort_unless($literacySubmission->status === 'approved', 409, 'Hasil literasi harus disetujui sebelum dinilai.');
        if ($literacySubmission->assessment && ! $request->filled('reason')) {
            throw ValidationException::withMessages(['reason' => 'Alasan perubahan wajib diisi agar koreksi nilai dapat ditelusuri.']);
        }
        $scores = $request->safe()->only(['focus_score', 'engagement_score', 'answer_score', 'retell_score', 'main_idea_score', 'summary_work_score']);
        $total = array_sum(array_map('intval', $scores));
        $score = ($total / 24) * 100;
        $displayScore = $setting->roundedScore($score);
        $assessment = DB::transaction(function () use ($request, $literacySubmission, $scores, $total, $score, $displayScore, $setting) {
            $assessment = LiteracyAssessment::where('literacy_submission_id', $literacySubmission->id)->lockForUpdate()->first();
            $action = $assessment ? 'update' : 'create';
            $assessment ??= new LiteracyAssessment([
                'literacy_submission_id' => $literacySubmission->id,
                'student_id' => $literacySubmission->student_id,
                'classroom_id' => $literacySubmission->assignment->classroom_id,
                'academic_year_id' => $literacySubmission->assignment->academic_year_id,
                'semester' => $literacySubmission->assignment->semester,
                'version' => 0,
            ]);
            $assessment->fill([
                ...$scores,
                'total_score' => $total,
                'score' => $score,
                'display_score' => $displayScore,
                'category' => $setting->category($displayScore),
                'feedback' => $request->input('feedback'),
                'assessed_by' => $request->user()->id,
                'assessed_at' => now(),
                'version' => $assessment->version + 1,
            ]);
            $assessment->save();
            LiteracyAssessmentRevision::create([
                'literacy_assessment_id' => $assessment->id,
                'user_id' => $request->user()->id,
                'version' => $assessment->version,
                'action' => $action,
                'reason' => $request->input('reason'),
                'snapshot' => $assessment->snapshot(),
            ]);
            ActivityLog::record($action === 'create' ? 'Menilai hasil literasi' : 'Mengoreksi nilai literasi', $literacySubmission->student->name.' · '.$literacySubmission->assignment->title);

            return $assessment;
        });

        return redirect()->route('evaluation.assessment', $literacySubmission)->with('success', 'Nilai versi '.$assessment->version.' berhasil disimpan.');
    }

    public function points(Request $request, Student $student): View
    {
        $setting = AssessmentSetting::current();
        abort_unless($setting, 409, 'Admin harus menetapkan aturan penilaian terlebih dahulu.');
        $student = Student::visibleTo($request->user())->with(['classroom.academicYear', 'literacySubmissions.assignment'])->findOrFail($student->id);
        $periodKey = $setting->periodKey(now(), $student->classroom->academicYear, $student->classroom->academicYear->semester ?? 'ganjil');
        $transactions = PointTransaction::visibleTo($request->user())->where('student_id', $student->id)->with(['recorder', 'corrections.recorder'])->latest('occurred_on')->latest('id')->get();
        $periodPoints = (int) $transactions->where('period_key', $periodKey)->sum('points');

        return view('manage.evaluation.points', [
            'student' => $student,
            'setting' => $setting,
            'transactions' => $transactions,
            'periodKey' => $periodKey,
            'periodPoints' => $periodPoints,
            'appreciation' => PointTransaction::appreciation($periodPoints),
            'activities' => PointTransaction::activities(),
            'idempotencyKey' => (string) Str::uuid(),
        ]);
    }

    public function addPoints(SavePointTransactionRequest $request, Student $student): RedirectResponse
    {
        $setting = AssessmentSetting::current();
        abort_unless($setting, 409, 'Admin harus menetapkan aturan penilaian terlebih dahulu.');
        $student = Student::visibleTo($request->user())->with('classroom.academicYear')->findOrFail($student->id);
        $date = Carbon::createFromFormat('Y-m-d', $request->input('occurred_on'))->startOfDay();
        $year = $student->classroom->academicYear;
        if ($date->lt($year->starts_on) || $date->gt($year->ends_on)) {
            throw ValidationException::withMessages(['occurred_on' => 'Tanggal aktivitas harus berada dalam tahun ajaran kelas murid.']);
        }
        $submission = null;
        if ($request->filled('literacy_submission_id')) {
            $submission = LiteracySubmission::visibleTo($request->user())->where('student_id', $student->id)->findOrFail($request->integer('literacy_submission_id'));
            abort_unless($submission->status === 'approved', 409, 'Sumber poin harus berupa hasil literasi yang sudah disetujui.');
        }
        $activity = PointTransaction::activities()[$request->input('activity_code')];
        $sourceKey = $submission ? 'literacy-submission:'.$submission->id : 'manual:'.$request->input('idempotency_key');
        $existing = PointTransaction::where('student_id', $student->id)->where('source_key', $sourceKey)->where('activity_code', $request->input('activity_code'))->first();
        if ($existing) {
            return back()->with('success', 'Aktivitas tersebut sudah tercatat; poin tidak digandakan.');
        }
        if ($setting->repeat_rule === 'once_daily' && PointTransaction::where('student_id', $student->id)->where('kind', 'earn')->where('activity_code', $request->input('activity_code'))->whereDate('occurred_on', $date)->exists()) {
            throw ValidationException::withMessages(['activity_code' => 'Aktivitas ini hanya dapat diberi poin sekali per hari untuk murid yang sama.']);
        }
        $semester = $year->semester ?? 'ganjil';
        DB::transaction(function () use ($request, $student, $submission, $activity, $sourceKey, $date, $year, $semester, $setting) {
            PointTransaction::create([
                'student_id' => $student->id,
                'classroom_id' => $student->classroom_id,
                'academic_year_id' => $year->id,
                'literacy_submission_id' => $submission?->id,
                'recorded_by' => $request->user()->id,
                'semester' => $semester,
                'kind' => 'earn',
                'activity_code' => $request->input('activity_code'),
                'points' => $activity['points'],
                'occurred_on' => $date,
                'period_key' => $setting->periodKey($date, $year, $semester),
                'source_key' => $sourceKey,
                'reason' => $request->input('reason'),
                'snapshot' => ['label' => $activity['label'], 'points' => $activity['points'], 'period' => $setting->periodLabel($date, $year, $semester)],
            ]);
            ActivityLog::record('Menambah poin aktivitas', $student->name.' · '.$activity['label'].' · +'.$activity['points']);
        });

        return back()->with('success', $activity['points'].' poin ditambahkan tanpa mengubah nilai kemampuan.');
    }

    public function correctPoints(Request $request, PointTransaction $pointTransaction): RedirectResponse
    {
        $pointTransaction = PointTransaction::visibleTo($request->user())->with('corrections')->findOrFail($pointTransaction->id);
        abort_unless($pointTransaction->kind === 'earn', 409, 'Koreksi harus merujuk pada transaksi poin awal.');
        $data = $request->validate(['corrected_points' => ['required', 'integer', 'between:0,100'], 'reason' => ['required', 'string', 'max:1000']]);
        if (! in_array((int) $data['corrected_points'], [0, $pointTransaction->points], true)) {
            throw ValidationException::withMessages(['corrected_points' => 'Koreksi hanya dapat membatalkan poin atau mengembalikannya ke nilai aktivitas yang berlaku.']);
        }
        $currentPoints = $pointTransaction->points + $pointTransaction->corrections->sum('points');
        $delta = (int) $data['corrected_points'] - $currentPoints;
        if ($delta === 0) {
            throw ValidationException::withMessages(['corrected_points' => 'Nilai koreksi sama dengan total poin saat ini.']);
        }
        DB::transaction(function () use ($request, $pointTransaction, $data, $currentPoints, $delta) {
            PointTransaction::create([
                'student_id' => $pointTransaction->student_id,
                'classroom_id' => $pointTransaction->classroom_id,
                'academic_year_id' => $pointTransaction->academic_year_id,
                'literacy_submission_id' => $pointTransaction->literacy_submission_id,
                'recorded_by' => $request->user()->id,
                'adjusts_transaction_id' => $pointTransaction->id,
                'semester' => $pointTransaction->semester,
                'kind' => 'correction',
                'activity_code' => $pointTransaction->activity_code,
                'points' => $delta,
                'occurred_on' => now()->toDateString(),
                'period_key' => $pointTransaction->period_key,
                'source_key' => 'correction:'.Str::uuid(),
                'reason' => $data['reason'],
                'snapshot' => ['before' => $currentPoints, 'after' => (int) $data['corrected_points'], 'delta' => $delta],
            ]);
            ActivityLog::record('Mengoreksi poin aktivitas', $pointTransaction->student->name.' · '.$currentPoints.' menjadi '.$data['corrected_points'].' · '.$data['reason']);
        });

        return back()->with('success', 'Koreksi poin disimpan sebagai transaksi baru.');
    }
}
