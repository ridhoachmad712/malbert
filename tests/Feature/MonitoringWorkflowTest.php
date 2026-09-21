<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AssessmentSetting;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\LiteracyAssessment;
use App\Models\LiteracyAssignment;
use App\Models\LiteracySubmission;
use App\Models\PointTransaction;
use App\Models\Student;
use App\Models\User;
use App\Services\MonitoringReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MonitoringWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{teacher: User, year: AcademicYear, classroom: Classroom, students: Collection<int, Student>, submissions: Collection<int, LiteracySubmission>} */
    private function context(): array
    {
        $this->travelTo(now()->setDate(2026, 9, 21)->startOfDay());
        $teacher = User::factory()->create(['role' => 'guru']);
        $year = AcademicYear::factory()->create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30']);
        $classroom = Classroom::factory()->for($year)->create(['name' => 'V A', 'grade' => 5, 'homeroom_teacher_id' => $teacher->id]);
        $students = collect([
            Student::factory()->for($classroom)->create(['name' => 'Alya']),
            Student::factory()->for($classroom)->create(['name' => 'Bima']),
            Student::factory()->for($classroom)->create(['name' => 'Citra']),
        ]);
        foreach ($students as $student) {
            Enrollment::factory()->for($student)->for($classroom)->create(['starts_on' => '2026-07-01']);
        }
        $assignment = LiteracyAssignment::create([
            'classroom_id' => $classroom->id, 'academic_year_id' => $year->id, 'created_by' => $teacher->id,
            'title' => 'Bacaan September', 'instructions' => 'Baca dan rangkum.', 'assigned_on' => '2026-09-01',
            'semester' => 'ganjil', 'status' => 'active',
        ]);
        $submissions = $students->take(2)->map(fn (Student $student, int $index) => LiteracySubmission::create([
            'literacy_assignment_id' => $assignment->id, 'student_id' => $student->id, 'recorded_by' => $teacher->id,
            'journal_date' => '2026-09-'.(10 + $index), 'summary' => 'Ringkasan', 'work_type' => 'none',
            'reflection_understanding' => 'Paham', 'reflection_feeling' => 'Senang', 'reflection_commitment' => 'Berlatih',
            'status' => 'approved', 'revision_number' => 1, 'submitted_at' => now(),
        ]));
        $this->assessment($submissions[0], $teacher, 80);
        $this->assessment($submissions[1], $teacher, 70);

        return compact('teacher', 'year', 'classroom', 'students', 'submissions');
    }

    private function assessment(LiteracySubmission $submission, User $teacher, float $score): LiteracyAssessment
    {
        return LiteracyAssessment::factory()->create([
            'literacy_submission_id' => $submission->id,
            'student_id' => $submission->student_id,
            'classroom_id' => $submission->assignment->classroom_id,
            'academic_year_id' => $submission->assignment->academic_year_id,
            'assessed_by' => $teacher->id,
            'display_score' => $score,
            'score' => $score,
            'category' => $score >= 76 ? 'good' : 'fair',
        ]);
    }

    private function setting(array $overrides = []): AssessmentSetting
    {
        return AssessmentSetting::factory()->create($overrides);
    }

    public function test_monitoring_waits_for_complete_rules_before_claiming_achievement(): void
    {
        $context = $this->context();
        AssessmentSetting::factory()->create(['monitoring_period' => null, 'student_score_method' => null, 'achievement_population' => null, 'unassessed_rule' => null]);

        $this->actingAs($context['teacher'])->get(route('monitoring.index'))
            ->assertOk()->assertSee('Aturan monitoring belum lengkap')->assertDontSee('Target minimal 80% tercapai');
        $this->get(route('monitoring.monthly'))->assertStatus(409);
    }

    public function test_enrolled_population_keeps_unassessed_students_distinct_and_uses_source_dates(): void
    {
        $context = $this->context();
        $setting = $this->setting(['monitoring_period' => 'month', 'achievement_population' => 'enrolled', 'unassessed_rule' => 'not_successful']);
        $earn = PointTransaction::factory()->create([
            'student_id' => $context['students'][0]->id, 'classroom_id' => $context['classroom']->id,
            'academic_year_id' => $context['year']->id, 'recorded_by' => $context['teacher']->id,
            'points' => 15, 'activity_code' => 'creative_work', 'occurred_on' => '2026-09-12',
        ]);
        PointTransaction::factory()->create([
            'student_id' => $context['students'][0]->id, 'classroom_id' => $context['classroom']->id,
            'academic_year_id' => $context['year']->id, 'recorded_by' => $context['teacher']->id,
            'kind' => 'correction', 'points' => -15, 'activity_code' => 'creative_work', 'occurred_on' => '2026-10-02',
            'adjusts_transaction_id' => $earn->id,
        ]);
        $service = app(MonitoringReport::class);
        $period = $service->configuredPeriod($setting, $context['year'], now());
        $report = $service->classroom($context['classroom'], $setting, $period);

        $this->assertSame(3, $report['population_count']);
        $this->assertSame(2, $report['assessed_count']);
        $this->assertSame(1, $report['unassessed_count']);
        $this->assertSame(1, $report['successful_count']);
        $this->assertSame(3, $report['denominator']);
        $this->assertSame(33.3, $report['achievement_percentage']);
        $this->assertSame(0, $report['point_total']);
        $this->actingAs($context['teacher'])->get(route('monitoring.index', ['date' => '2026-09-21']))
            ->assertOk()->assertSee('33,3%')->assertSee('Belum dinilai')->assertSee('Alya')->assertSee('Citra');
    }

    public function test_population_and_unassessed_choices_change_only_the_documented_denominator(): void
    {
        $context = $this->context();
        $setting = $this->setting(['monitoring_period' => 'month', 'achievement_population' => 'enrolled', 'unassessed_rule' => 'exclude']);
        $service = app(MonitoringReport::class);
        $period = $service->configuredPeriod($setting, $context['year'], now());

        $excluded = $service->classroom($context['classroom'], $setting, $period);
        $this->assertSame(2, $excluded['denominator']);
        $this->assertSame(50.0, $excluded['achievement_percentage']);

        $setting->update(['achievement_population' => 'participants', 'unassessed_rule' => 'not_successful']);
        $participants = $service->classroom($context['classroom'], $setting->fresh(), $period);
        $this->assertSame(2, $participants['population_count']);
        $this->assertSame(2, $participants['denominator']);
        $this->assertFalse($participants['rows']->contains(fn (array $row) => $row['student']->name === 'Citra'));
    }

    public function test_score_method_uses_average_or_latest_activity_in_period(): void
    {
        $context = $this->context();
        $secondAssignment = LiteracyAssignment::create([
            'classroom_id' => $context['classroom']->id, 'academic_year_id' => $context['year']->id, 'created_by' => $context['teacher']->id,
            'title' => 'Bacaan lanjutan', 'instructions' => 'Baca.', 'assigned_on' => '2026-09-15', 'semester' => 'ganjil', 'status' => 'active',
        ]);
        $submission = LiteracySubmission::create([
            'literacy_assignment_id' => $secondAssignment->id, 'student_id' => $context['students'][0]->id, 'recorded_by' => $context['teacher']->id,
            'journal_date' => '2026-09-20', 'summary' => 'Ringkasan kedua', 'work_type' => 'none',
            'reflection_understanding' => 'Paham', 'reflection_feeling' => 'Senang', 'reflection_commitment' => 'Berlatih',
            'status' => 'approved', 'revision_number' => 1, 'submitted_at' => now(),
        ]);
        $this->assessment($submission, $context['teacher'], 60);
        $setting = $this->setting(['monitoring_period' => 'month', 'student_score_method' => 'average']);
        $service = app(MonitoringReport::class);
        $period = $service->configuredPeriod($setting, $context['year'], now());
        $average = $service->classroom($context['classroom'], $setting, $period)['rows']->firstWhere(fn (array $row) => $row['student']->is($context['students'][0]));
        $this->assertSame(70.0, $average['score']);

        $setting->update(['student_score_method' => 'latest']);
        $latest = $service->classroom($context['classroom'], $setting->fresh(), $period)['rows']->firstWhere(fn (array $row) => $row['student']->is($context['students'][0]));
        $this->assertSame(60.0, $latest['score']);
    }

    public function test_teacher_access_is_limited_to_assigned_classes_and_students(): void
    {
        $context = $this->context();
        $this->setting();
        $outsider = User::factory()->create(['role' => 'guru']);

        $this->actingAs($outsider)->get(route('monitoring.index', ['classroom' => $context['classroom']->id]))->assertNotFound();
        $this->get(route('monitoring.student', ['classroom' => $context['classroom'], 'student' => $context['students'][0]]))->assertNotFound();
        $this->actingAs($context['teacher'])->get(route('monitoring.student', ['classroom' => $context['classroom'], 'student' => $context['students'][0]]))->assertOk()->assertSee('Riwayat bulanan');
    }

    public function test_monthly_report_renders_and_csv_neutralizes_spreadsheet_formulas(): void
    {
        $context = $this->context();
        $this->setting();
        $context['students'][0]->update(['name' => '=SUM(A1:A2)']);
        $this->actingAs($context['teacher']);

        $this->get(route('monitoring.monthly', ['classroom' => $context['classroom']->id, 'month' => '2026-09']))
            ->assertOk()->assertSee('Laporan bulanan')->assertSee('Belum dinilai')->assertDontSee('<script>', false);
        $response = $this->get(route('monitoring.export', ['classroom' => $context['classroom']->id, 'month' => '2026-09']))
            ->assertOk()->assertDownload('laporan-v-a-2026-09.csv');
        $this->assertStringContainsString("'=SUM(A1:A2)", $response->streamedContent());
    }

    public function test_monitoring_configuration_is_preserved_by_backup_version_five(): void
    {
        Storage::fake('local');
        $this->context();
        $setting = $this->setting(['monitoring_period' => 'semester', 'student_score_method' => 'latest', 'achievement_population' => 'participants', 'unassessed_rule' => 'exclude']);
        $this->artisan('school:backup')->assertSuccessful();
        $backup = Storage::disk('local')->files('backups')[0];
        foreach (['point_transactions', 'literacy_assessment_revisions', 'literacy_assessments', 'assessment_settings', 'literacy_reviews', 'literacy_submissions', 'literacy_assignments', 'reading_resources', 'enrollments', 'students', 'classroom_user', 'classrooms', 'academic_years', 'activity_logs', 'publications', 'school_settings', 'users'] as $table) {
            DB::table($table)->delete();
        }
        $this->artisan('school:backup', ['--restore' => Storage::disk('local')->path($backup)])->assertSuccessful();

        $this->assertDatabaseHas('assessment_settings', [
            'id' => $setting->id, 'monitoring_period' => 'semester', 'student_score_method' => 'latest',
            'achievement_population' => 'participants', 'unassessed_rule' => 'exclude',
        ]);
    }
}
