<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveLiteracyAssignmentRequest;
use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\LiteracyAssignment;
use App\Models\ReadingResource;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LiteracyAssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('manage.literacy.index', [
            'assignments' => LiteracyAssignment::visibleTo($user)->with(['classroom.academicYear', 'readingResource'])->withCount('submissions')->latest('assigned_on')->get(),
            'readings' => ReadingResource::with('author')->when($user->role !== 'admin', fn ($query) => $query->where('user_id', $user->id))->latest()->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return $this->form($request, new LiteracyAssignment);
    }

    public function edit(Request $request, LiteracyAssignment $literacyAssignment): View
    {
        $literacyAssignment = LiteracyAssignment::visibleTo($request->user())->findOrFail($literacyAssignment->id);

        return $this->form($request, $literacyAssignment);
    }

    public function save(SaveLiteracyAssignmentRequest $request, ?LiteracyAssignment $literacyAssignment = null): RedirectResponse
    {
        $user = $request->user();
        if ($literacyAssignment) {
            $literacyAssignment = LiteracyAssignment::visibleTo($user)->findOrFail($literacyAssignment->id);
            $classroom = $literacyAssignment->classroom;
        } else {
            $classroom = Classroom::visibleTo($user)->with('academicYear')->findOrFail($request->integer('classroom_id'));
        }
        $data = $request->safe()->only(['reading_resource_id', 'title', 'instructions', 'assigned_on', 'due_at', 'status']);
        if ($data['reading_resource_id'] ?? null) {
            $reading = ReadingResource::findOrFail($data['reading_resource_id']);
            abort_unless($user->role === 'admin' || $reading->is_public || $reading->user_id === $user->id, 404);
        }
        if ($data['assigned_on'] < $classroom->academicYear->starts_on->toDateString() || $data['assigned_on'] > $classroom->academicYear->ends_on->toDateString()) {
            throw ValidationException::withMessages(['assigned_on' => 'Tanggal penugasan harus berada dalam tahun ajaran kelas.']);
        }
        if (($data['due_at'] ?? null) && Carbon::parse($data['due_at'])->toDateString() > $classroom->academicYear->ends_on->toDateString()) {
            throw ValidationException::withMessages(['due_at' => 'Batas waktu harus berada dalam tahun ajaran kelas.']);
        }
        if (! $literacyAssignment && ! in_array($classroom->academicYear->semester, ['ganjil', 'genap'], true)) {
            throw ValidationException::withMessages(['classroom_id' => 'Semester tahun ajaran kelas belum ditetapkan.']);
        }
        $literacyAssignment = DB::transaction(function () use ($literacyAssignment, $classroom, $user, $data) {
            if ($literacyAssignment) {
                $literacyAssignment = LiteracyAssignment::lockForUpdate()->findOrFail($literacyAssignment->id);
                $literacyAssignment->update($data);
            } else {
                $literacyAssignment = LiteracyAssignment::create([
                    ...$data,
                    'classroom_id' => $classroom->id,
                    'academic_year_id' => $classroom->academic_year_id,
                    'semester' => $classroom->academicYear->semester,
                    'created_by' => $user->id,
                ]);
            }
            ActivityLog::record('Menyimpan tugas literasi', $literacyAssignment->title.' · '.$classroom->name);

            return $literacyAssignment;
        });

        return redirect()->route('literacy.assignments.show', $literacyAssignment)->with('success', 'Tugas literasi berhasil disimpan.');
    }

    public function show(Request $request, LiteracyAssignment $literacyAssignment): View
    {
        $literacyAssignment = LiteracyAssignment::visibleTo($request->user())->with(['classroom.academicYear', 'readingResource', 'submissions.student', 'submissions.assessment', 'submissions.publication', 'submissions.reviews.user'])->findOrFail($literacyAssignment->id);
        $currentStudents = $literacyAssignment->classroom->students()->where('active', true)->orderBy('name')->get();
        $students = $currentStudents->merge($literacyAssignment->submissions->pluck('student'))->unique('id')->sortBy('name');

        return view('manage.literacy.assignment-detail', compact('literacyAssignment', 'students'));
    }

    private function form(Request $request, LiteracyAssignment $literacyAssignment): View
    {
        $user = $request->user();
        $readings = ReadingResource::when($user->role !== 'admin', fn ($query) => $query->where(fn ($query) => $query->where('user_id', $user->id)->orWhere('is_public', true)))->orderBy('title')->get();

        return view('manage.literacy.assignment', [
            'assignment' => $literacyAssignment,
            'classrooms' => Classroom::visibleTo($user)->with('academicYear')->orderBy('grade')->orderBy('name')->get(),
            'readings' => $readings,
        ]);
    }
}
