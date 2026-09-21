<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AssessmentSetting;
use App\Models\Classroom;
use App\Models\LiteracyAssessment;
use App\Models\LiteracyAssignment;
use App\Models\LiteracySubmission;
use App\Models\PointTransaction;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class EvaluationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{teacher: User, classroom: Classroom, student: Student, assignment: LiteracyAssignment, submission: LiteracySubmission} */
    private function context(string $submissionStatus = 'approved'): array
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        $year = AcademicYear::factory()->create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'semester' => 'ganjil']);
        $classroom = Classroom::factory()->for($year)->create(['name' => 'V A', 'grade' => 5, 'homeroom_teacher_id' => $teacher->id]);
        $student = Student::factory()->for($classroom)->create(['name' => 'Murid Penilaian']);
        $assignment = LiteracyAssignment::create([
            'classroom_id' => $classroom->id, 'academic_year_id' => $year->id, 'created_by' => $teacher->id,
            'title' => 'Tugas Penilaian', 'instructions' => 'Baca dan ceritakan kembali.', 'assigned_on' => '2026-09-01',
            'semester' => 'ganjil', 'status' => 'active',
        ]);
        $submission = LiteracySubmission::create([
            'literacy_assignment_id' => $assignment->id, 'student_id' => $student->id, 'recorded_by' => $teacher->id,
            'journal_date' => '2026-09-20', 'summary' => 'Ringkasan hasil membaca.', 'work_type' => 'none',
            'reflection_understanding' => 'Paham', 'reflection_feeling' => 'Senang', 'reflection_commitment' => 'Membaca lagi',
            'status' => $submissionStatus, 'revision_number' => 1, 'submitted_at' => now(),
        ]);

        return compact('teacher', 'classroom', 'student', 'assignment', 'submission');
    }

    /** @return array<string, int|string> */
    private function scores(int $score = 4): array
    {
        return [
            'focus_score' => $score, 'engagement_score' => $score, 'answer_score' => $score,
            'retell_score' => $score, 'main_idea_score' => $score, 'summary_work_score' => $score,
            'feedback' => 'Pertahankan kebiasaan membaca dan bercerita.',
        ];
    }

    private function setting(array $overrides = []): AssessmentSetting
    {
        return AssessmentSetting::factory()->create($overrides);
    }

    public function test_admin_must_configure_rules_and_teacher_cannot_change_them(): void
    {
        $context = $this->context();
        $admin = User::factory()->create(['role' => 'admin']);
        $payload = ['score_rounding' => 'whole', 'success_threshold' => 76, 'point_period' => 'semester', 'repeat_rule' => 'once_daily'];

        $this->actingAs($context['teacher'])->get(route('evaluation.index'))->assertOk()->assertSee('Aturan sekolah belum ditetapkan');
        $this->get(route('evaluation.assessment', $context['submission']))->assertStatus(409);
        $this->get(route('evaluation.settings'))->assertForbidden();
        $this->put(route('evaluation.settings.update'), $payload)->assertForbidden();
        $this->actingAs($admin)->put(route('evaluation.settings.update'), $payload)->assertSessionHasNoErrors()->assertRedirect(route('evaluation.index'));

        $this->assertDatabaseHas('assessment_settings', ['score_rounding' => 'whole', 'success_threshold' => 76, 'point_period' => 'semester', 'repeat_rule' => 'once_daily']);
    }

    public function test_assessment_uses_six_indicators_formula_category_and_versioned_correction(): void
    {
        $context = $this->context();
        $this->setting(['score_rounding' => 'whole']);
        $payload = $this->scores(4);
        $payload['summary_work_score'] = 3;

        $this->actingAs($context['teacher'])->put(route('evaluation.assessment.update', $context['submission']), $payload)->assertSessionHasNoErrors()->assertRedirect();
        $assessment = LiteracyAssessment::firstOrFail();
        $this->assertSame(23, $assessment->total_score);
        $this->assertSame('95.83', $assessment->score);
        $this->assertSame('96.0', $assessment->display_score);
        $this->assertSame('very_good', $assessment->category);
        $this->assertSame(1, $assessment->version);

        $this->put(route('evaluation.assessment.update', $context['submission']), $this->scores(3))->assertSessionHasErrors('reason');
        $this->put(route('evaluation.assessment.update', $context['submission']), [...$this->scores(3), 'reason' => 'Bukti observasi diperiksa kembali.'])->assertSessionHasNoErrors();
        $this->assertSame(2, $assessment->fresh()->version);
        $this->assertSame('75.0', $assessment->fresh()->display_score);
        $this->assertSame('fair', $assessment->fresh()->category);
        $this->assertDatabaseCount('literacy_assessment_revisions', 2);
        $this->assertDatabaseHas('literacy_assessment_revisions', ['version' => 2, 'action' => 'update', 'reason' => 'Bukti observasi diperiksa kembali.']);
    }

    public function test_one_decimal_rounding_is_applied_before_category(): void
    {
        $context = $this->context();
        $this->setting(['score_rounding' => 'one_decimal']);
        $payload = $this->scores(3);
        $payload['focus_score'] = 4;
        $this->actingAs($context['teacher'])->put(route('evaluation.assessment.update', $context['submission']), $payload)->assertSessionHasNoErrors();

        $assessment = LiteracyAssessment::firstOrFail();
        $this->assertSame('79.17', $assessment->score);
        $this->assertSame('79.2', $assessment->display_score);
        $this->assertSame('good', $assessment->category);
    }

    public function test_only_approved_visible_submission_can_be_assessed(): void
    {
        $context = $this->context('submitted');
        $this->setting();
        $outsider = User::factory()->create(['role' => 'guru']);

        $this->actingAs($context['teacher'])->get(route('evaluation.assessment', $context['submission']))->assertStatus(409);
        $context['submission']->update(['status' => 'approved']);
        $this->actingAs($outsider)->get(route('evaluation.assessment', $context['submission']))->assertNotFound();
        $this->put(route('evaluation.assessment.update', $context['submission']), $this->scores())->assertNotFound();
    }

    public function test_points_are_separate_idempotent_and_linked_to_source(): void
    {
        $context = $this->context();
        $this->setting(['point_period' => 'month', 'repeat_rule' => 'distinct_event']);
        $key = (string) Str::uuid();
        $payload = ['activity_code' => 'summary', 'occurred_on' => '2026-09-21', 'literacy_submission_id' => $context['submission']->id, 'idempotency_key' => $key];

        $this->actingAs($context['teacher'])->post(route('evaluation.points.store', $context['student']), $payload)->assertSessionHasNoErrors();
        $this->post(route('evaluation.points.store', $context['student']), $payload)->assertSessionHasNoErrors();

        $this->assertDatabaseCount('point_transactions', 1);
        $this->assertDatabaseHas('point_transactions', ['student_id' => $context['student']->id, 'activity_code' => 'summary', 'points' => 10, 'period_key' => '2026-09', 'source_key' => 'literacy-submission:'.$context['submission']->id]);
        $this->assertDatabaseCount('literacy_assessments', 0);
    }

    public function test_daily_repeat_rule_blocks_duplicate_activity_but_distinct_event_can_allow_it(): void
    {
        $context = $this->context();
        $setting = $this->setting(['repeat_rule' => 'once_daily']);
        $base = ['activity_code' => 'read_15', 'occurred_on' => '2026-09-21'];
        $this->actingAs($context['teacher'])->post(route('evaluation.points.store', $context['student']), $base + ['idempotency_key' => (string) Str::uuid()])->assertSessionHasNoErrors();
        $this->post(route('evaluation.points.store', $context['student']), $base + ['idempotency_key' => (string) Str::uuid()])->assertSessionHasErrors('activity_code');
        $setting->update(['repeat_rule' => 'distinct_event']);
        $this->post(route('evaluation.points.store', $context['student']), $base + ['idempotency_key' => (string) Str::uuid()])->assertSessionHasNoErrors();

        $this->assertDatabaseCount('point_transactions', 2);
    }

    public function test_point_correction_is_append_only_and_changes_appreciation_total(): void
    {
        $context = $this->context();
        $this->setting(['point_period' => 'month', 'repeat_rule' => 'distinct_event']);
        $this->actingAs($context['teacher']);
        foreach (['creative_work', 'retell', 'discipline'] as $activity) {
            $this->post(route('evaluation.points.store', $context['student']), ['activity_code' => $activity, 'occurred_on' => '2026-09-21', 'idempotency_key' => (string) Str::uuid()])->assertSessionHasNoErrors();
        }
        $transaction = PointTransaction::where('activity_code', 'retell')->firstOrFail();
        $this->post(route('evaluation.points.correct', $transaction), ['corrected_points' => 0, 'reason' => 'Aktivitas tercatat pada murid yang salah.'])->assertSessionHasNoErrors();

        $this->assertSame(20, (int) PointTransaction::where('student_id', $context['student']->id)->sum('points'));
        $this->assertSame('Bintang Literasi', PointTransaction::appreciation(20));
        $this->assertDatabaseHas('point_transactions', ['kind' => 'earn', 'points' => 10]);
        $this->assertDatabaseHas('point_transactions', ['kind' => 'correction', 'points' => -10, 'adjusts_transaction_id' => $transaction->id, 'reason' => 'Aktivitas tercatat pada murid yang salah.']);
    }

    public function test_evaluation_pages_render_and_escape_private_names(): void
    {
        $context = $this->context();
        $this->setting();
        $context['student']->update(['name' => '<script>Murid</script>']);
        $this->actingAs($context['teacher']);

        foreach ([route('evaluation.index'), route('evaluation.assessment', $context['submission']), route('evaluation.points', $context['student'])] as $url) {
            $this->get($url)->assertOk()->assertDontSee('<script>', false);
        }
        $this->get('/')->assertDontSee('Murid', false);
    }

    public function test_backup_round_trip_restores_evaluation_rules_scores_and_point_history(): void
    {
        Storage::fake('local');
        $context = $this->context();
        $setting = $this->setting();
        $assessment = LiteracyAssessment::factory()->create([
            'literacy_submission_id' => $context['submission']->id,
            'student_id' => $context['student']->id,
            'classroom_id' => $context['classroom']->id,
            'academic_year_id' => $context['classroom']->academic_year_id,
            'assessed_by' => $context['teacher']->id,
        ]);
        $revision = $assessment->revisions()->create(['user_id' => $context['teacher']->id, 'version' => 1, 'action' => 'create', 'snapshot' => $assessment->snapshot()]);
        $point = PointTransaction::factory()->create([
            'student_id' => $context['student']->id, 'classroom_id' => $context['classroom']->id,
            'academic_year_id' => $context['classroom']->academic_year_id, 'recorded_by' => $context['teacher']->id,
        ]);
        $this->artisan('school:backup')->assertSuccessful();
        $backup = Storage::disk('local')->files('backups')[0];
        foreach (['point_transactions', 'literacy_assessment_revisions', 'literacy_assessments', 'assessment_settings', 'literacy_reviews', 'literacy_submissions', 'literacy_assignments', 'reading_resources', 'enrollments', 'students', 'classroom_user', 'classrooms', 'academic_years', 'activity_logs', 'publications', 'school_settings', 'users'] as $table) {
            DB::table($table)->delete();
        }
        $this->artisan('school:backup', ['--restore' => Storage::disk('local')->path($backup)])->assertSuccessful();

        $this->assertDatabaseHas('assessment_settings', ['id' => $setting->id]);
        $this->assertDatabaseHas('literacy_assessments', ['id' => $assessment->id, 'student_id' => $context['student']->id]);
        $this->assertDatabaseHas('literacy_assessment_revisions', ['id' => $revision->id]);
        $this->assertDatabaseHas('point_transactions', ['id' => $point->id, 'points' => 5]);
    }
}
