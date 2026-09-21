<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FoundationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->role === 'admin', 403);

        return view('manage.foundation', [
            'years' => AcademicYear::orderByDesc('starts_on')->get(),
            'classrooms' => Classroom::with(['academicYear', 'homeroomTeacher', 'teachers'])->withCount('students')->orderByDesc('academic_year_id')->orderBy('grade')->orderBy('name')->get(),
        ]);
    }

    public function storeYear(Request $request): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate([
            'name' => ['required', 'regex:~^20[0-9]{2}/20[0-9]{2}$~', Rule::unique('academic_years')],
            'starts_on' => 'required|date_format:Y-m-d',
            'ends_on' => 'required|date_format:Y-m-d|after:starts_on',
        ]);
        [$first, $last] = explode('/', $data['name']);
        if ((int) $last !== (int) $first + 1 || substr($data['starts_on'], 0, 4) !== $first || substr($data['ends_on'], 0, 4) !== $last) {
            throw ValidationException::withMessages(['name' => 'Gunakan tahun berurutan, misalnya 2026/2027, dengan tanggal awal dan akhir pada tahun tersebut.']);
        }
        DB::transaction(function () use ($data) {
            AcademicYear::create($data);
            ActivityLog::record('Menambah tahun ajaran', $data['name']);
        });

        return back()->with('success', 'Tahun ajaran ditambahkan. Pilih semester untuk menjadikannya periode berjalan.');
    }

    public function activateYear(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate(['semester' => 'required|in:ganjil,genap']);
        DB::transaction(function () use ($academicYear, $data) {
            AcademicYear::orderBy('id')->lockForUpdate()->get();
            AcademicYear::where('is_current', true)->update(['is_current' => null]);
            $academicYear->refresh()->update(['is_current' => true, 'semester' => $data['semester']]);
            ActivityLog::record('Mengatur periode berjalan', $academicYear->name.' · '.$data['semester']);
        }, 3);

        return back()->with('success', 'Periode berjalan diperbarui. Keanggotaan murid tetap; gunakan pindah kelas untuk kenaikan kelas.');
    }

    public function editClassroom(Request $request, ?Classroom $classroom = null): View
    {
        abort_unless($request->user()->role === 'admin', 403);

        return view('manage.classroom', [
            'classroom' => $classroom?->load('teachers') ?? new Classroom,
            'years' => AcademicYear::orderByDesc('starts_on')->get(),
            'teachers' => User::where('role', 'guru')->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function saveClassroom(Request $request, ?Classroom $classroom = null): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $yearId = $classroom?->academic_year_id ?? $request->input('academic_year_id');
        $teacherRule = fn () => Rule::exists('users', 'id')->where('role', 'guru')->where('active', true);
        $data = $request->validate([
            'academic_year_id' => [$classroom ? 'prohibited' : 'required', 'integer', 'exists:academic_years,id'],
            'name' => ['required', 'string', 'max:40', Rule::unique('classrooms')->where('academic_year_id', is_scalar($yearId) ? $yearId : null)->ignore($classroom?->id)],
            'grade' => 'required|integer|between:1,6',
            'homeroom_teacher_id' => ['nullable', 'integer', $teacherRule()],
            'teacher_ids' => 'sometimes|array|max:100',
            'teacher_ids.*' => ['required', 'integer', 'distinct', $teacherRule()],
        ]);
        DB::transaction(function () use ($classroom, $data) {
            $teachers = $data['teacher_ids'] ?? [];
            unset($data['teacher_ids']);
            if ($classroom) {
                $classroom = Classroom::whereKey($classroom->id)->lockForUpdate()->firstOrFail();
                $classroom->update($data);
            } else {
                $classroom = Classroom::create($data);
            }
            $classroom->teachers()->sync($teachers);
            ActivityLog::record('Mengatur rombel dan penugasan', $classroom->academicYear->name.' · '.$classroom->name);
        });

        return redirect()->route('foundation.index')->with('success', 'Rombel dan penugasan guru disimpan.');
    }
}
