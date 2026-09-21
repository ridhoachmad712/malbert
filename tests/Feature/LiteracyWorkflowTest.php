<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\LiteracyAssignment;
use App\Models\LiteracySubmission;
use App\Models\ReadingResource;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LiteracyWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{0: User, 1: Classroom, 2: Student} */
    private function classContext(): array
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        $year = AcademicYear::factory()->create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'semester' => 'ganjil']);
        $classroom = Classroom::factory()->for($year)->create(['name' => 'IV A', 'grade' => 4, 'homeroom_teacher_id' => $teacher->id]);
        $student = Student::factory()->for($classroom)->create(['name' => 'Ayu Membaca']);

        return [$teacher, $classroom, $student];
    }

    private function assignment(User $teacher, Classroom $classroom, ?ReadingResource $reading = null): LiteracyAssignment
    {
        return LiteracyAssignment::create([
            'classroom_id' => $classroom->id,
            'academic_year_id' => $classroom->academic_year_id,
            'reading_resource_id' => $reading?->id,
            'created_by' => $teacher->id,
            'title' => 'Membaca cerita nusantara',
            'instructions' => 'Baca dan tuliskan pelajaran yang ditemukan.',
            'assigned_on' => '2026-09-21',
            'due_at' => '2026-09-28 12:00:00',
            'semester' => 'ganjil',
            'status' => 'active',
        ]);
    }

    /** @return array<string, mixed> */
    private function completeSubmission(string $intent = 'submit'): array
    {
        return [
            'journal_date' => '2026-09-21',
            'duration_minutes' => 25,
            'summary' => 'Tokoh utama belajar menepati janji kepada temannya.',
            'work_type' => 'text',
            'work_text' => 'Surat singkat untuk tokoh utama.',
            'reflection_understanding' => 'Saya memahami pentingnya menepati janji.',
            'reflection_feeling' => 'Saya senang karena tokohnya mau memperbaiki kesalahan.',
            'reflection_commitment' => 'Saya akan menepati janji kepada teman.',
            'intent' => $intent,
        ];
    }

    public function test_only_approved_readings_appear_publicly_and_teacher_cannot_publish(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'guru']);
        ReadingResource::factory()->for($admin, 'author')->create(['title' => 'Bacaan Publik', 'is_public' => true, 'published_at' => now()]);
        ReadingResource::factory()->for($teacher, 'author')->create(['title' => 'Bacaan Internal']);

        $this->get(route('readings.public'))->assertOk()->assertSee('Bacaan Publik')->assertDontSee('Bacaan Internal');
        $this->actingAs($teacher)->post(route('readings.store'), ['title' => 'Usulan Guru', 'url' => 'https://example.com/usulan', 'is_public' => '1'])->assertSessionHasErrors('is_public');
        $this->assertDatabaseMissing('reading_resources', ['title' => 'Usulan Guru']);
    }

    public function test_admin_can_publish_reading_and_public_qr_is_controlled(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('readings.store'), [
            'title' => 'Petualangan Air', 'description' => 'Mengenal perjalanan air.',
            'url' => 'https://example.com/petualangan-air', 'grade' => 4,
            'qr' => UploadedFile::fake()->image('qr.png', 300, 300), 'is_public' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $reading = ReadingResource::firstOrFail();
        $this->assertNotNull($reading->published_at);
        Storage::disk('local')->assertExists($reading->qr_path);
        $this->get(route('readings.qr', $reading))->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_assignment_snapshots_class_period_and_rejects_dates_outside_year(): void
    {
        [$teacher, $classroom] = $this->classContext();
        $payload = ['classroom_id' => $classroom->id, 'title' => 'Catatan membaca', 'instructions' => 'Isi jurnal dan refleksi.', 'assigned_on' => '2026-09-21', 'due_at' => '2026-09-30 12:00', 'status' => 'active'];
        $this->actingAs($teacher)->post(route('literacy.assignments.store'), $payload)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('literacy_assignments', ['classroom_id' => $classroom->id, 'academic_year_id' => $classroom->academic_year_id, 'semester' => 'ganjil']);
        $this->post(route('literacy.assignments.store'), [...$payload, 'assigned_on' => '2028-01-01', 'due_at' => null])->assertSessionHasErrors('assigned_on');
    }

    public function test_unassigned_teacher_cannot_access_class_literacy_data(): void
    {
        [$teacher, $classroom, $student] = $this->classContext();
        $outsider = User::factory()->create(['role' => 'guru']);
        $assignment = $this->assignment($teacher, $classroom);
        $this->actingAs($outsider)->get(route('literacy.assignments.show', $assignment))->assertNotFound();
        $this->get(route('literacy.submissions.edit', [$assignment, $student]))->assertNotFound();
        $this->put(route('literacy.submissions.update', [$assignment, $student]), $this->completeSubmission())->assertNotFound();
    }

    public function test_complete_submission_review_revision_and_approval_keeps_snapshots(): void
    {
        [$teacher, $classroom, $student] = $this->classContext();
        $assignment = $this->assignment($teacher, $classroom);
        $this->actingAs($teacher)->put(route('literacy.submissions.update', [$assignment, $student]), $this->completeSubmission())->assertSessionHasNoErrors()->assertRedirect();
        $submission = LiteracySubmission::firstOrFail();
        $this->assertSame('submitted', $submission->status);
        $this->assertSame(1, $submission->revision_number);
        $this->post(route('literacy.submissions.review', $submission), ['action' => 'changes', 'note' => 'Tambahkan contoh dari cerita.'])->assertSessionHasNoErrors();
        $revision = $this->completeSubmission();
        $revision['summary'] = 'Tokoh menepati janji dan meminta maaf kepada temannya.';
        $this->put(route('literacy.submissions.update', [$assignment, $student]), $revision)->assertSessionHasNoErrors();
        $this->assertSame(2, $submission->fresh()->revision_number);
        $this->post(route('literacy.submissions.review', $submission), ['action' => 'approve', 'note' => 'Sudah jelas.'])->assertSessionHasNoErrors();
        $this->assertSame('approved', $submission->fresh()->status);
        $this->assertDatabaseCount('literacy_reviews', 4);
        $this->assertSame(['submit', 'changes', 'submit', 'approve'], DB::table('literacy_reviews')->where('literacy_submission_id', $submission->id)->orderBy('id')->pluck('action')->all());
    }

    public function test_submitting_requires_complete_journal_and_reflection(): void
    {
        [$teacher, $classroom, $student] = $this->classContext();
        $assignment = $this->assignment($teacher, $classroom);
        $this->actingAs($teacher)->put(route('literacy.submissions.update', [$assignment, $student]), ['work_type' => 'none', 'intent' => 'submit'])
            ->assertSessionHasErrors(['journal_date', 'summary', 'reflection_understanding', 'reflection_feeling', 'reflection_commitment']);
        $this->assertDatabaseCount('literacy_submissions', 0);
    }

    public function test_work_file_is_private_and_available_only_to_assigned_staff(): void
    {
        Storage::fake('local');
        [$teacher, $classroom, $student] = $this->classContext();
        $assignment = $this->assignment($teacher, $classroom);
        $payload = $this->completeSubmission('draft');
        $payload['work_type'] = 'pdf';
        $payload['work_file'] = UploadedFile::fake()->createWithContent('karya.pdf', '%PDF-1.4 karya');
        $this->actingAs($teacher)->put(route('literacy.submissions.update', [$assignment, $student]), $payload)->assertSessionHasNoErrors();
        $submission = LiteracySubmission::firstOrFail();
        Storage::disk('local')->assertExists($submission->work_path);
        $this->get(route('literacy.submissions.attachment', $submission))->assertDownload('karya.pdf')->assertHeader('Cache-Control', 'no-store, private');
        $this->actingAs(User::factory()->create())->get(route('literacy.submissions.attachment', $submission))->assertNotFound();
        $this->get('/storage/'.$submission->work_path)->assertForbidden();
    }

    public function test_literacy_pages_render_escape_names_and_reject_mismatched_work_files(): void
    {
        Storage::fake('local');
        [$teacher, $classroom, $student] = $this->classContext();
        $student->update(['name' => '<script>Murid</script>']);
        $reading = ReadingResource::factory()->for($teacher, 'author')->create(['title' => '<script>Bacaan</script>']);
        $assignment = $this->assignment($teacher, $classroom, $reading);
        $this->actingAs($teacher);

        foreach ([
            route('literacy.index'), route('readings.create'), route('readings.edit', $reading),
            route('literacy.assignments.create'), route('literacy.assignments.edit', $assignment),
            route('literacy.assignments.show', $assignment), route('literacy.submissions.edit', [$assignment, $student]),
        ] as $url) {
            $this->get($url)->assertOk()->assertDontSee('<script>', false);
        }
        $payload = $this->completeSubmission();
        $payload['work_type'] = 'photo';
        $payload['work_file'] = UploadedFile::fake()->createWithContent('karya.pdf', '%PDF-1.4 karya');
        $this->put(route('literacy.submissions.update', [$assignment, $student]), $payload)->assertSessionHasErrors('work_file');
        $this->assertDatabaseCount('literacy_submissions', 0);
    }

    public function test_backup_round_trip_restores_literacy_records_and_private_work(): void
    {
        Storage::fake('local');
        [$teacher, $classroom, $student] = $this->classContext();
        $reading = ReadingResource::factory()->for($teacher, 'author')->create();
        $assignment = $this->assignment($teacher, $classroom, $reading);
        $submission = LiteracySubmission::create([
            'literacy_assignment_id' => $assignment->id, 'student_id' => $student->id, 'recorded_by' => $teacher->id,
            'journal_date' => '2026-09-21', 'summary' => 'Ringkasan', 'work_type' => 'pdf',
            'work_path' => 'literacy/submissions/11111111-1111-1111-1111-111111111111.pdf', 'work_original_name' => 'karya.pdf',
            'reflection_understanding' => 'Paham', 'reflection_feeling' => 'Senang', 'reflection_commitment' => 'Membaca lagi', 'status' => 'draft',
        ]);
        Storage::disk('local')->put($submission->work_path, '%PDF-1.4 karya');
        $this->artisan('school:backup')->assertSuccessful();
        $backup = Storage::disk('local')->files('backups')[0];
        Storage::disk('local')->delete($submission->work_path);
        foreach (['literacy_reviews', 'literacy_submissions', 'literacy_assignments', 'reading_resources', 'enrollments', 'students', 'classroom_user', 'classrooms', 'academic_years', 'activity_logs', 'publications', 'school_settings', 'users'] as $table) {
            DB::table($table)->delete();
        }
        $this->artisan('school:backup', ['--restore' => Storage::disk('local')->path($backup)])->assertSuccessful();
        $this->assertDatabaseHas('literacy_submissions', ['id' => $submission->id, 'work_original_name' => 'karya.pdf']);
        Storage::disk('local')->assertExists($submission->work_path);
    }
}
