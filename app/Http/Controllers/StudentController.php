<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveStudentRequest;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\Student;
use App\Services\StudentImportService;
use App\Services\StudentRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => 'nullable|string|max:120', 'classroom' => 'nullable|integer', 'year' => 'nullable|integer', 'status' => 'nullable|in:active,inactive,pending,verified']);
        $query = Student::visibleTo($request->user())->with('classroom.academicYear');
        if (! empty($filters['q'])) {
            $term = '%'.$filters['q'].'%';
            $query->where(fn (Builder $query) => $query->where('name', 'like', $term)->orWhere('nis', 'like', $term)->orWhere('nisn', 'like', $term));
        }
        $query->when($filters['classroom'] ?? null, fn (Builder $query, string $id) => $query->where('classroom_id', $id));
        $query->when($filters['year'] ?? null, fn (Builder $query, string $id) => $query->whereHas('classroom', fn (Builder $classrooms) => $classrooms->where('academic_year_id', $id)));
        match ($filters['status'] ?? null) {
            'active' => $query->where('active', true),
            'inactive' => $query->where('active', false),
            'pending' => $query->whereNull('verified_at'),
            'verified' => $query->whereNotNull('verified_at'),
            default => null,
        };

        return view('manage.students', [
            'students' => $query->orderBy('name')->orderBy('id')->paginate(20)->withQueryString(),
            'classrooms' => Classroom::visibleTo($request->user())->with('academicYear')->orderByDesc('academic_year_id')->orderBy('grade')->orderBy('name')->get(),
            'years' => AcademicYear::whereHas('classrooms', fn (Builder $query) => $query->visibleTo($request->user()))->orderByDesc('starts_on')->get(),
        ]);
    }

    public function edit(Request $request, ?Student $student = null): View
    {
        if ($student) {
            abort_unless(Student::visibleTo($request->user())->whereKey($student->id)->exists(), 404);
            $student->load(['classroom.academicYear', 'verifier']);
        }

        return view('manage.student', [
            'student' => $student ?? new Student,
            'classrooms' => Classroom::visibleTo($request->user())->with('academicYear')->orderByDesc('academic_year_id')->orderBy('grade')->orderBy('name')->get(),
            'history' => $student ? $student->enrollments()->whereHas('classroom', fn (Builder $query) => $query->visibleTo($request->user()))->with('classroom.academicYear')->get() : collect(),
        ]);
    }

    public function save(SaveStudentRequest $request, StudentRegistry $registry, ?Student $student = null): RedirectResponse
    {
        $data = $request->validated();
        try {
            $student = DB::transaction(function () use ($student, $request, $data, $registry) {
                if (! $student) {
                    return $registry->create($request->user(), $data);
                }
                $student = Student::whereKey($student->id)->lockForUpdate()->firstOrFail();
                $registry->classroom($request->user(), $student->classroom_id);
                $student->fill(['name' => $data['name'], 'nis' => $data['nis'] ?? null, 'nisn' => $data['nisn'] ?? null]);
                if ($request->user()->role === 'admin') {
                    $student->active = $data['active'];
                    $verified = (bool) $data['verified'];
                    if (! $verified || ! $student->verified_at || $student->isDirty(['name', 'nis', 'nisn'])) {
                        $student->verified_at = $verified ? now() : null;
                        $student->verified_by = $verified ? $request->user()->id : null;
                    }
                } elseif ($student->isDirty(['name', 'nis', 'nisn'])) {
                    $student->verified_at = null;
                    $student->verified_by = null;
                }
                $student->save();
                ActivityLog::record('Memperbarui data murid', 'Murid #'.$student->id);

                return $student;
            });
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages(['nis' => 'NIS atau NISN sudah digunakan. Periksa daftar murid sebelum menyimpan ulang.']);
        }

        return redirect()->route('students.edit', $student)->with('success', 'Data murid disimpan.');
    }

    public function transfer(Request $request, Student $student, StudentRegistry $registry): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate(['classroom_id' => 'required|integer|exists:classrooms,id', 'starts_on' => 'required|date_format:Y-m-d|before_or_equal:today']);
        $registry->transfer($student, $request->user(), (int) $data['classroom_id'], $data['starts_on']);

        return redirect()->route('students.edit', $student)->with('success', 'Kelas diperbarui dan riwayat sebelumnya disimpan.');
    }

    public function import(Request $request, StudentImportService $importer): RedirectResponse
    {
        $data = $request->validate(['classroom_id' => 'required|integer|exists:classrooms,id', 'starts_on' => 'required|date_format:Y-m-d|before_or_equal:today', 'file' => 'required|file|extensions:csv|max:2048']);
        $count = $importer->import($request->user(), (int) $data['classroom_id'], $data['starts_on'], $request->file('file'));

        return redirect()->route('students.index', ['classroom' => $data['classroom_id']])->with('success', $count.' murid berhasil diimpor dan menunggu verifikasi admin.');
    }

    public function template(): StreamedResponse
    {
        return response()->streamDownload(function () {
            echo "\xEF\xBB\xBFnama,nis,nisn\r\n";
        }, 'template-murid.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
