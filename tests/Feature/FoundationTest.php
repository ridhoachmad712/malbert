<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    private function classroom(?User $teacher = null): Classroom
    {
        $this->travelTo(now()->setDate(2026, 9, 20)->startOfDay());
        $year = AcademicYear::factory()->create(['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30']);

        return Classroom::factory()->for($year)->create(['homeroom_teacher_id' => $teacher?->id]);
    }

    private function studentPayload(Classroom $classroom): array
    {
        return ['name' => 'Murid Uji', 'nis' => '000123', 'nisn' => '0012345678', 'classroom_id' => $classroom->id, 'starts_on' => '2026-07-01'];
    }

    public function test_admin_can_create_year_and_set_one_current_semester(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $old = AcademicYear::factory()->create(['name' => '2025/2026', 'starts_on' => '2025-07-01', 'ends_on' => '2026-06-30', 'is_current' => true]);

        $this->actingAs($admin)->post(route('years.store'), ['name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30'])->assertSessionHasNoErrors()->assertRedirect();
        $year = AcademicYear::where('name', '2026/2027')->firstOrFail();
        $this->put(route('years.activate', $year), ['semester' => 'genap'])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('academic_years', ['id' => $year->id, 'is_current' => 1, 'semester' => 'genap']);
        $this->assertNull($old->fresh()->is_current);
    }

    public function test_invalid_year_and_dates_are_not_saved(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post(route('years.store'), ['name' => '2026/2028', 'starts_on' => '2026-07-01', 'ends_on' => '2028-06-30'])
            ->assertSessionHasErrors(['name' => 'Gunakan tahun berurutan, misalnya 2026/2027, dengan tanggal awal dan akhir pada tahun tersebut.']);

        $this->assertDatabaseCount('academic_years', 0);
    }

    public function test_admin_assigns_and_revokes_teachers_without_changing_class_year(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create();
        $assistant = User::factory()->create();
        $classroom = $this->classroom();

        $this->actingAs($admin)->put(route('classrooms.update', $classroom), ['name' => 'I B', 'grade' => 1, 'homeroom_teacher_id' => $teacher->id, 'teacher_ids' => [$assistant->id]])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('classrooms', ['id' => $classroom->id, 'name' => 'I B', 'homeroom_teacher_id' => $teacher->id]);
        $this->assertDatabaseHas('classroom_user', ['classroom_id' => $classroom->id, 'user_id' => $assistant->id]);

        $this->put(route('classrooms.update', $classroom), ['name' => 'I B', 'grade' => 1, 'homeroom_teacher_id' => null])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('classroom_user', 0);
        $this->assertFalse(Classroom::visibleTo($teacher)->exists());
        $this->assertFalse(Classroom::visibleTo($assistant)->exists());
    }

    public function test_duplicate_class_names_and_inactive_teacher_assignments_are_rejected(): void
    {
        $classroom = $this->classroom();
        $inactive = User::factory()->create(['active' => false]);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->post(route('classrooms.store'), ['academic_year_id' => $classroom->academic_year_id, 'name' => $classroom->name, 'grade' => 8, 'homeroom_teacher_id' => $inactive->id])->assertSessionHasErrors(['name', 'grade', 'homeroom_teacher_id']);

        $this->assertDatabaseCount('classrooms', 1);
    }

    public function test_guest_cannot_access_operational_routes(): void
    {
        $student = Student::factory()->create();
        foreach (['students.index', 'students.create', 'students.template', 'foundation.index', 'classrooms.create'] as $name) {
            $this->get(route($name))->assertRedirect(route('login'));
        }
        $this->get(route('students.edit', $student))->assertRedirect(route('login'));
        $this->post(route('students.store'), [])->assertRedirect(route('login'));
        $this->put(route('students.update', $student), [])->assertRedirect(route('login'));
        $this->post(route('students.import'), [])->assertRedirect(route('login'));
        $this->post(route('students.transfer', $student), [])->assertRedirect(route('login'));
        $this->get('/')->assertDontSee($student->nis);
        $this->assertDatabaseCount('students', 1);
    }

    public function test_teacher_cannot_manage_foundation_or_transfer_students(): void
    {
        $teacher = User::factory()->create();
        $classroom = $this->classroom($teacher);
        $student = Student::factory()->for($classroom)->create();
        $this->actingAs($teacher);

        $this->get(route('foundation.index'))->assertForbidden();
        $this->get(route('classrooms.create'))->assertForbidden();
        $this->get(route('classrooms.edit', $classroom))->assertForbidden();
        $this->post(route('years.store'), [])->assertForbidden();
        $this->put(route('years.activate', $classroom->academic_year_id), ['semester' => 'genap'])->assertForbidden();
        $this->post(route('classrooms.store'), [])->assertForbidden();
        $this->put(route('classrooms.update', $classroom), [])->assertForbidden();
        $this->post(route('students.transfer', $student), [])->assertForbidden();

        $this->assertDatabaseCount('academic_years', 1);
        $this->assertDatabaseCount('classrooms', 1);
        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_assigned_teacher_creates_student_with_leading_zero_ids_and_history(): void
    {
        $teacher = User::factory()->create();
        $classroom = $this->classroom($teacher);

        $this->actingAs($teacher)->post(route('students.store'), $this->studentPayload($classroom) + ['verified_by' => $teacher->id])->assertSessionHasNoErrors()->assertRedirect();

        $student = Student::firstOrFail();
        $this->assertSame('000123', $student->nis);
        $this->assertSame('0012345678', $student->nisn);
        $this->assertNull($student->verified_by);
        $this->assertDatabaseHas('enrollments', ['student_id' => $student->id, 'classroom_id' => $classroom->id, 'ends_on' => null, 'recorded_by' => $teacher->id]);
        $this->assertSame('2026-07-01', $student->enrollments()->firstOrFail()->starts_on->toDateString());
        $this->assertDatabaseHas('activity_logs', ['user_id' => $teacher->id, 'action' => 'Mendaftarkan murid']);
    }

    public function test_teacher_cannot_view_edit_create_or_import_into_unassigned_class(): void
    {
        $teacher = User::factory()->create();
        $classroom = $this->classroom();
        $student = Student::factory()->for($classroom)->create(['name' => 'Murid Kelas Lain']);
        $this->actingAs($teacher);

        $this->get(route('students.index', ['q' => 'Murid', 'classroom' => $classroom->id]))->assertDontSee('Murid Kelas Lain');
        $this->get(route('students.edit', $student))->assertNotFound();
        $this->put(route('students.update', $student), ['name' => 'Diubah', 'nis' => $student->nis])->assertNotFound();
        $this->post(route('students.store'), $this->studentPayload($classroom))->assertNotFound();
        $this->post(route('students.import'), ['classroom_id' => $classroom->id, 'starts_on' => '2026-07-01', 'file' => UploadedFile::fake()->createWithContent('murid.csv', "nama,nis,nisn\nMurid,0001,\n")])->assertNotFound();

        $this->assertDatabaseCount('students', 1);
        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'Murid Kelas Lain']);
        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_companion_teacher_can_search_assigned_students_but_not_others(): void
    {
        $teacher = User::factory()->create();
        $classroom = $this->classroom();
        $classroom->teachers()->attach($teacher);
        $otherClassroom = Classroom::factory()->for($classroom->academicYear)->create(['name' => 'I B']);
        Student::factory()->for($classroom)->create(['name' => 'Anak Kelas A', 'nis' => '00123']);
        Student::factory()->for($otherClassroom)->create(['name' => 'Anak Kelas B', 'nis' => '00456']);

        $this->actingAs($teacher)->get(route('students.index', ['q' => '00123', 'year' => $classroom->academic_year_id, 'status' => 'pending']))->assertSee('Anak Kelas A')->assertDontSee('Anak Kelas B');
    }

    public function test_identity_changes_by_teacher_reset_admin_verification(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create();
        $classroom = $this->classroom($teacher);
        $student = Student::factory()->for($classroom)->create(['verified_at' => now(), 'verified_by' => $admin->id]);

        $this->actingAs($teacher)->put(route('students.update', $student), ['name' => 'Nama Diperbaiki', 'nis' => $student->nis])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'Nama Diperbaiki', 'verified_at' => null, 'verified_by' => null]);
    }

    public function test_admin_verifies_and_deactivates_student_without_losing_history(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $enrollment = Enrollment::factory()->create();
        $student = $enrollment->student;

        $this->actingAs($admin)->put(route('students.update', $student), ['name' => $student->name, 'nis' => $student->nis, 'active' => 0, 'verified' => 1])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('students', ['id' => $student->id, 'active' => 0, 'verified_by' => $admin->id]);
        $this->assertNotNull($student->fresh()->verified_at);
        $this->assertModelExists($enrollment);
    }

    #[TestWith(['verified', 1])]
    #[TestWith(['active', 0])]
    #[TestWith(['classroom_id', 999])]
    public function test_teacher_cannot_change_protected_student_fields(string $field, int $value): void
    {
        $teacher = User::factory()->create();
        $student = Student::factory()->for($this->classroom($teacher))->create();

        $this->actingAs($teacher)->put(route('students.update', $student), ['name' => 'Jangan Disimpan', 'nis' => $student->nis, $field => $value])->assertSessionHasErrors($field);

        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => $student->name, 'active' => true, 'verified_by' => null]);
    }

    public function test_transfer_retains_history_and_moves_teacher_access(): void
    {
        $teacher = User::factory()->create();
        $classroom = $this->classroom($teacher);
        $nextYear = AcademicYear::factory()->create(['name' => '2027/2028', 'starts_on' => '2027-07-01', 'ends_on' => '2028-06-30']);
        $newTeacher = User::factory()->create();
        $destination = Classroom::factory()->for($nextYear)->create(['name' => 'II A', 'grade' => 2, 'homeroom_teacher_id' => $newTeacher->id]);
        $student = Student::factory()->for($classroom)->create();
        $entry = Enrollment::factory()->for($student)->create(['starts_on' => '2026-07-01']);
        $this->travelTo(now()->setDate(2027, 7, 10));

        $this->actingAs(User::factory()->create(['role' => 'admin']))->post(route('students.transfer', $student), ['classroom_id' => $destination->id, 'starts_on' => '2027-07-01'])->assertSessionHasNoErrors();

        $this->assertSame('2027-07-01', $entry->fresh()->ends_on->toDateString());
        $this->assertDatabaseHas('enrollments', ['student_id' => $student->id, 'classroom_id' => $destination->id, 'ends_on' => null]);
        $this->assertSame('2027-07-01', $student->enrollments()->whereNull('ends_on')->firstOrFail()->starts_on->toDateString());
        $this->assertDatabaseCount('students', 1);
        $this->actingAs($teacher)->get(route('students.edit', $student))->assertNotFound();
        $this->actingAs($newTeacher)->get(route('students.edit', $student))->assertSee('II A')->assertDontSee('2026/2027');
    }

    public function test_invalid_transfer_leaves_original_membership_untouched(): void
    {
        $classroom = $this->classroom();
        $student = Student::factory()->for($classroom)->create();
        $entry = Enrollment::factory()->for($student)->create(['starts_on' => '2026-08-01']);
        $destination = Classroom::factory()->for($classroom->academicYear)->create(['name' => 'I B']);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->post(route('students.transfer', $student), ['classroom_id' => $destination->id, 'starts_on' => '2026-07-01'])->assertSessionHasErrors('classroom_id');
        $this->post(route('students.transfer', $student), ['classroom_id' => $destination->id, 'starts_on' => '2025-07-01'])->assertSessionHasErrors('starts_on');

        $this->assertDatabaseCount('enrollments', 1);
        $this->assertNull($entry->fresh()->ends_on);
        $this->assertSame($classroom->id, $student->fresh()->classroom_id);
    }

    public function test_year_filter_uses_current_class_while_admin_retains_previous_year_history(): void
    {
        $classroom = $this->classroom();
        $student = Student::factory()->for($classroom)->create(['name' => 'Murid Lintas Tahun']);
        Enrollment::factory()->for($student)->create(['starts_on' => '2026-07-01']);
        $nextYear = AcademicYear::factory()->create(['name' => '2027/2028', 'starts_on' => '2027-07-01', 'ends_on' => '2028-06-30']);
        $destination = Classroom::factory()->for($nextYear)->create(['name' => 'II A', 'grade' => 2]);
        $this->travelTo(now()->setDate(2027, 7, 10));
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->post(route('students.transfer', $student), ['classroom_id' => $destination->id, 'starts_on' => '2027-07-01'])->assertSessionHasNoErrors();

        $this->get(route('students.index', ['year' => $classroom->academic_year_id]))
            ->assertOk()->assertSee('Tahun kelas saat ini')->assertDontSee('Murid Lintas Tahun');
        $this->get(route('students.index', ['year' => $nextYear->id]))->assertOk()->assertSee('Murid Lintas Tahun');
        $this->get(route('students.edit', $student))->assertOk()->assertSeeInOrder(['Perjalanan kelas', 'II A', '2027/2028', $classroom->name, '2026/2027']);
        $this->assertDatabaseCount('enrollments', 2);
    }

    #[TestWith(['ganjil'])]
    #[TestWith(['genap'])]
    public function test_switching_semester_does_not_move_students_or_rewrite_membership(string $semester): void
    {
        $classroom = $this->classroom();
        $classroom->academicYear->update(['is_current' => true]);
        $student = Student::factory()->for($classroom)->create();
        $entry = Enrollment::factory()->for($student)->create(['starts_on' => '2026-07-01']);
        $original = $entry->fresh()->getAttributes();

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->put(route('years.activate', $classroom->academic_year_id), ['semester' => $semester])
            ->assertSessionHasNoErrors();

        $this->assertSame($semester, $classroom->academicYear->fresh()->semester);
        $this->assertTrue($classroom->academicYear->fresh()->is_current);
        $this->assertSame(1, AcademicYear::where('is_current', true)->count());
        $this->assertSame($classroom->id, $student->fresh()->classroom_id);
        $this->assertSame($original, $entry->fresh()->getAttributes());
        $this->assertDatabaseCount('enrollments', 1);
    }

    #[TestWith([','])]
    #[TestWith([';'])]
    public function test_csv_import_preserves_zeroes_and_is_safe_to_retry(string $separator): void
    {
        $teacher = User::factory()->create();
        $classroom = $this->classroom($teacher);
        $contents = "\xEF\xBB\xBF".implode($separator, ['nama', 'nis', 'nisn'])."\n".implode($separator, ['Murid CSV A', '000001', '0012345678'])."\n".implode($separator, ['Murid CSV B', '000002', ''])."\n";
        $payload = ['classroom_id' => $classroom->id, 'starts_on' => '2026-07-01', 'file' => UploadedFile::fake()->createWithContent('murid.csv', $contents)];
        $this->actingAs($teacher);

        $this->post(route('students.import'), $payload)->assertSessionHasNoErrors()->assertRedirect(route('students.index', ['classroom' => $classroom->id]));
        $this->assertDatabaseHas('students', ['nis' => '000001', 'nisn' => '0012345678', 'verified_at' => null]);
        $this->assertDatabaseCount('enrollments', 2);
        $this->post(route('students.import'), $payload)->assertSessionHasErrors('file.2');
        $this->assertDatabaseCount('students', 2);
    }

    public function test_csv_validation_reports_rows_and_rolls_back_entire_file(): void
    {
        $teacher = User::factory()->create();
        $classroom = $this->classroom($teacher);
        $csv = "nama,nis,nisn\nMurid Valid,000001,\nMurid Duplikat,000001,\nMurid Invalid,,12\n";

        $this->actingAs($teacher)->post(route('students.import'), ['classroom_id' => $classroom->id, 'starts_on' => '2026-07-01', 'file' => UploadedFile::fake()->createWithContent('murid.csv', $csv)])
            ->assertSessionHasErrors(['file.3.nis' => 'Baris 3: NIS sama dengan baris 2.', 'file.4']);

        $this->assertDatabaseCount('students', 0);
        $this->assertDatabaseCount('enrollments', 0);
        $this->assertDatabaseCount('activity_logs', 0);
    }

    #[TestWith(['nama,nis,nisn', 'Berkas belum berisi data murid. Isi setidaknya satu baris.'])]
    #[TestWith(['nama,nomor', 'Judul kolom harus berurutan: nama, nis, nisn. Gunakan template yang tersedia.'])]
    public function test_empty_or_wrong_csv_has_actionable_error(string $csv, string $message): void
    {
        $teacher = User::factory()->create();
        $classroom = $this->classroom($teacher);

        $this->actingAs($teacher)->post(route('students.import'), ['classroom_id' => $classroom->id, 'starts_on' => '2026-07-01', 'file' => UploadedFile::fake()->createWithContent('murid.csv', $csv)])->assertSessionHasErrors(['file' => $message]);

        $this->assertDatabaseCount('students', 0);
    }

    public function test_pages_render_and_escape_student_and_class_names(): void
    {
        $classroom = $this->classroom();
        $classroom->update(['name' => '<script>kelas</script>']);
        $student = Student::factory()->for($classroom)->create(['name' => '<script>murid</script>']);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        foreach (['foundation.index', 'classrooms.create', 'students.index', 'students.create'] as $name) {
            $this->get(route($name))->assertOk()->assertDontSee('<script>kelas</script>', false)->assertDontSee('<script>murid</script>', false);
        }
        $this->get(route('students.edit', $student))->assertSee('&lt;script&gt;murid&lt;/script&gt;', false);
        $this->get(route('classrooms.edit', $classroom))->assertSee('&lt;script&gt;kelas&lt;/script&gt;', false);
        $this->get(route('students.template'))->assertDownload('template-murid.csv')->assertStreamedContent("\xEF\xBB\xBFnama,nis,nisn\r\n");
    }

    public function test_backup_round_trip_includes_students_assignments_and_history(): void
    {
        Storage::fake('local');
        $teacher = User::factory()->create();
        $classroom = $this->classroom($teacher);
        $classroom->teachers()->attach($teacher);
        $student = Student::factory()->for($classroom)->create();
        $entry = Enrollment::factory()->for($student)->create();
        $this->artisan('school:backup')->assertSuccessful();
        $backup = Storage::disk('local')->files('backups')[0];
        foreach (['enrollments', 'students', 'classroom_user', 'classrooms', 'academic_years', 'users'] as $table) {
            DB::table($table)->delete();
        }

        $this->artisan('school:backup', ['--restore' => Storage::disk('local')->path($backup)])->assertSuccessful();

        $this->assertDatabaseHas('students', ['id' => $student->id, 'nis' => $student->nis]);
        $this->assertDatabaseHas('enrollments', ['id' => $entry->id, 'student_id' => $student->id, 'classroom_id' => $classroom->id]);
        $this->assertDatabaseHas('classroom_user', ['classroom_id' => $classroom->id, 'user_id' => $teacher->id]);
        Storage::disk('local')->assertExists($backup);
    }
}
