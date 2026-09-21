<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\LiteracyAssignment;
use App\Models\LiteracySubmission;
use App\Models\Publication;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{teacher: User, admin: User, submission: LiteracySubmission} */
    private function context(array $submissionOverrides = []): array
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        $admin = User::factory()->create(['role' => 'admin']);
        $year = AcademicYear::factory()->create(['name' => '2026/2027']);
        $classroom = Classroom::factory()->for($year)->create(['name' => 'IV A', 'grade' => 4, 'homeroom_teacher_id' => $teacher->id]);
        $student = Student::factory()->for($classroom)->create(['name' => 'Ayu Rahasia', 'nis' => '99112233', 'nisn' => '0099887766']);
        $assignment = LiteracyAssignment::factory()->for($classroom)->for($year, 'academicYear')->for($teacher, 'creator')->create(['title' => 'Cerita Nusantara']);
        $submission = LiteracySubmission::factory()->for($assignment, 'assignment')->for($student)->for($teacher, 'recorder')->create(array_replace([
            'journal_date' => '2026-09-21',
            'work_type' => 'text',
            'work_text' => 'Karya publik pilihan Ayu.',
            'reflection_understanding' => 'Refleksi sangat privat.',
            'reflection_feeling' => 'Perasaan sangat privat.',
            'reflection_commitment' => 'Komitmen sangat privat.',
            'feedback' => 'Umpan balik guru privat.',
            'status' => 'approved',
            'revision_number' => 2,
            'reviewed_at' => now(),
            'reviewed_by' => $admin->id,
        ], $submissionOverrides));

        return compact('teacher', 'admin', 'submission');
    }

    /** @return array<string, mixed> */
    private function publicationPayload(array $overrides = []): array
    {
        return array_replace([
            'title' => 'Karya dari Cerita Nusantara',
            'type' => 'karya',
            'summary' => 'Karya siswa IV A dari kegiatan literasi.',
            'body' => 'Karya ini dipilih setelah tinjauan.',
            'event_date' => '2026-09-21',
            'school_year' => '2026/2027',
            'classes' => ['4'],
            'source_credit_mode' => 'class',
            'permission_confirmed' => '1',
            'intent' => 'submit',
        ], $overrides);
    }

    public function test_approved_work_becomes_traceable_publication_without_internal_data(): void
    {
        $context = $this->context();
        $this->actingAs($context['teacher'])->get(route('publications.bank'))->assertOk()->assertSee('Cerita Nusantara');
        $this->post(route('publications.from-work', $context['submission']))->assertSessionHasNoErrors()->assertRedirect();
        $publication = Publication::firstOrFail();

        $this->assertSame($context['submission']->id, $publication->literacy_submission_id);
        $this->assertSame(2, $publication->source_revision_number);
        $this->assertSame('Karya publik pilihan Ayu.', $publication->content['selected_work']['text']);
        $this->assertArrayNotHasKey('reflection_understanding', $publication->source_snapshot);
        $this->assertArrayNotHasKey('feedback', $publication->source_snapshot);

        $this->put(route('publications.update', $publication), $this->publicationPayload())->assertSessionHasNoErrors();
        $this->actingAs($context['admin'])->post(route('publications.review', $publication), ['action' => 'publish', 'media_checked' => '1'])->assertSessionHasNoErrors();
        $this->get(route('story', $publication->slug))->assertOk()
            ->assertSee('Karya publik pilihan Ayu.')
            ->assertSee('Siswa IV A')
            ->assertDontSee('Ayu Rahasia')
            ->assertDontSee('Refleksi sangat privat.')
            ->assertDontSee('Umpan balik guru privat.')
            ->assertDontSee('0099887766');

        $this->actingAs($context['teacher'])->put(route('publications.update', $publication), $this->publicationPayload(['title' => 'Revisi belum disetujui']))->assertSessionHasNoErrors();
        $this->get(route('story', $publication->slug))->assertSee('Karya dari Cerita Nusantara')->assertDontSee('Revisi belum disetujui');
        $this->post(route('publications.from-work', $context['submission']))->assertRedirect(route('publications.edit', $publication));
        $this->assertSame(1, Publication::count());
    }

    public function test_student_name_requires_separate_identity_confirmation(): void
    {
        $context = $this->context();
        $this->actingAs($context['teacher'])->post(route('publications.from-work', $context['submission']));
        $publication = Publication::firstOrFail();

        $this->put(route('publications.update', $publication), $this->publicationPayload(['source_credit_mode' => 'student']))->assertSessionHasErrors('student_identity_confirmed');
        $this->put(route('publications.update', $publication), $this->publicationPayload(['source_credit_mode' => 'student', 'student_identity_confirmed' => '1']))->assertSessionHasNoErrors();
        $this->assertSame('Ayu Rahasia', $publication->fresh()->content['source_credit']);
    }

    public function test_selected_file_is_copied_and_only_served_from_authorized_publication_version(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('literacy/submissions/source.pdf', '%PDF source');
        $context = $this->context(['work_type' => 'pdf', 'work_text' => null, 'work_path' => 'literacy/submissions/source.pdf', 'work_original_name' => 'Karya Ayu.pdf']);
        $this->actingAs($context['teacher'])->post(route('publications.from-work', $context['submission']))->assertSessionHasNoErrors();
        $publication = Publication::firstOrFail();
        $path = $publication->content['selected_work']['path'];
        Storage::disk('local')->assertExists('literacy/submissions/source.pdf');
        Storage::disk('local')->assertExists($path);
        $url = route('publication.work', [$publication, basename($path)]);
        $this->get($url)->assertOk();
        auth()->logout();
        $this->get($url)->assertNotFound();

        $this->actingAs($context['teacher'])->put(route('publications.update', $publication), $this->publicationPayload())->assertSessionHasNoErrors();
        $this->actingAs($context['admin'])->post(route('publications.review', $publication), ['action' => 'publish', 'media_checked' => '1']);
        auth()->logout();
        $this->get($url)->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->artisan('school:backup')->assertSuccessful();
        $backup = Storage::disk('local')->files('backups')[0];
        $zip = new \ZipArchive;
        $this->assertTrue($zip->open(Storage::disk('local')->path($backup)) === true);
        $records = json_decode($zip->getFromName('records.json'), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame(6, $records['version']);
        $this->assertNotFalse($zip->locateName($path));
        $zip->close();
    }

    public function test_unapproved_work_cannot_be_selected(): void
    {
        $context = $this->context(['status' => 'submitted']);
        $this->actingAs($context['teacher'])->post(route('publications.from-work', $context['submission']))->assertSessionHasErrors('work');
        $this->assertSame(0, Publication::count());
    }
}
