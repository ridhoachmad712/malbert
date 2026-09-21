<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveLiteracySubmissionRequest;
use App\Models\ActivityLog;
use App\Models\LiteracyAssignment;
use App\Models\LiteracyReview;
use App\Models\LiteracySubmission;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LiteracySubmissionController extends Controller
{
    public function edit(Request $request, LiteracyAssignment $literacyAssignment, Student $student): View
    {
        $literacyAssignment = LiteracyAssignment::visibleTo($request->user())->findOrFail($literacyAssignment->id);
        $submission = LiteracySubmission::where('literacy_assignment_id', $literacyAssignment->id)->where('student_id', $student->id)->with('reviews.user')->first();
        abort_unless($submission || ($student->classroom_id === $literacyAssignment->classroom_id && $student->active), 404);

        return view('manage.literacy.submission', [
            'assignment' => $literacyAssignment->load('classroom'),
            'student' => $student,
            'submission' => $submission ?? new LiteracySubmission(['work_type' => 'none', 'status' => 'draft']),
        ]);
    }

    public function save(SaveLiteracySubmissionRequest $request, LiteracyAssignment $literacyAssignment, Student $student): RedirectResponse
    {
        $literacyAssignment = LiteracyAssignment::visibleTo($request->user())->findOrFail($literacyAssignment->id);
        $submission = LiteracySubmission::where('literacy_assignment_id', $literacyAssignment->id)->where('student_id', $student->id)->first();
        abort_unless($submission || ($student->classroom_id === $literacyAssignment->classroom_id && $student->active), 404);
        abort_if($submission && in_array($submission->status, ['submitted', 'approved'], true), 409, 'Hasil yang sedang atau sudah ditinjau tidak dapat diubah.');
        $data = $request->safe()->only([
            'journal_date', 'duration_minutes', 'summary', 'work_type', 'work_text', 'work_url',
            'reflection_understanding', 'reflection_feeling', 'reflection_commitment',
        ]);
        $data['work_text'] = $data['work_type'] === 'text' ? ($data['work_text'] ?? null) : null;
        $data['work_url'] = $data['work_type'] === 'link' ? ($data['work_url'] ?? null) : null;
        $newPath = null;
        $originalName = null;
        if ($request->hasFile('work_file')) {
            $extension = strtolower($request->file('work_file')->extension());
            $newPath = $request->file('work_file')->storeAs('literacy/submissions', Str::uuid().'.'.$extension, 'local');
            if (! $newPath) {
                throw ValidationException::withMessages(['work_file' => 'Berkas karya gagal disimpan. Silakan coba lagi.']);
            }
            $originalName = mb_substr($request->file('work_file')->getClientOriginalName(), 0, 255);
        }
        $usesFile = in_array($data['work_type'], ['photo', 'pdf'], true);
        $existingPath = $submission?->work_path;
        $existingMatchesType = $existingPath && ($data['work_type'] === 'pdf' ? str_ends_with(strtolower($existingPath), '.pdf') : ! str_ends_with(strtolower($existingPath), '.pdf'));
        if ($request->input('intent') === 'submit' && $usesFile && ! $newPath && ! $existingMatchesType) {
            throw ValidationException::withMessages(['work_file' => 'Unggah berkas karya sebelum mengajukan hasil.']);
        }
        $oldPath = null;
        try {
            $submission = DB::transaction(function () use ($submission, $request, $literacyAssignment, $student, $data, $usesFile, $newPath, $originalName, &$oldPath) {
                if ($submission) {
                    $submission = LiteracySubmission::lockForUpdate()->findOrFail($submission->id);
                } else {
                    $submission = new LiteracySubmission([
                        'literacy_assignment_id' => $literacyAssignment->id,
                        'student_id' => $student->id,
                        'recorded_by' => $request->user()->id,
                    ]);
                }
                if (! $usesFile && $submission->work_path) {
                    $oldPath = $submission->work_path;
                    $submission->work_path = null;
                    $submission->work_original_name = null;
                }
                if ($newPath) {
                    $oldPath = $submission->work_path;
                    $submission->work_path = $newPath;
                    $submission->work_original_name = $originalName;
                }
                $submission->fill($data);
                if ($request->input('intent') === 'submit') {
                    $submission->status = 'submitted';
                    $submission->revision_number++;
                    $submission->submitted_at = now();
                    $submission->reviewed_at = null;
                    $submission->reviewed_by = null;
                    $submission->feedback = null;
                } else {
                    $submission->status = 'draft';
                }
                $submission->recorded_by = $request->user()->id;
                $submission->save();
                if ($request->input('intent') === 'submit') {
                    LiteracyReview::create([
                        'literacy_submission_id' => $submission->id,
                        'user_id' => $request->user()->id,
                        'action' => 'submit',
                        'snapshot' => $submission->snapshot(),
                    ]);
                }
                ActivityLog::record($request->input('intent') === 'submit' ? 'Mengajukan hasil literasi' : 'Menyimpan draf literasi', $student->name.' · '.$literacyAssignment->title);

                return $submission;
            });
        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }
            throw $exception;
        }
        if ($oldPath) {
            Storage::disk('local')->delete($oldPath);
        }

        return redirect()->route('literacy.submissions.edit', [$literacyAssignment, $student])->with('success', $request->input('intent') === 'submit' ? 'Hasil diajukan untuk ditinjau.' : 'Draf hasil literasi disimpan.');
    }

    public function review(Request $request, LiteracySubmission $literacySubmission): RedirectResponse
    {
        $literacySubmission = LiteracySubmission::visibleTo($request->user())->findOrFail($literacySubmission->id);
        $data = $request->validate([
            'action' => ['required', 'in:approve,changes'],
            'note' => ['nullable', 'required_if:action,changes', 'string', 'max:5000'],
        ]);
        DB::transaction(function () use ($literacySubmission, $request, $data) {
            $literacySubmission = LiteracySubmission::lockForUpdate()->findOrFail($literacySubmission->id);
            abort_unless($literacySubmission->status === 'submitted', 409, 'Hasil harus berstatus diajukan.');
            $literacySubmission->status = $data['action'] === 'approve' ? 'approved' : 'changes';
            $literacySubmission->reviewed_at = now();
            $literacySubmission->reviewed_by = $request->user()->id;
            $literacySubmission->feedback = $data['note'] ?? null;
            $literacySubmission->save();
            LiteracyReview::create([
                'literacy_submission_id' => $literacySubmission->id,
                'user_id' => $request->user()->id,
                'action' => $data['action'],
                'note' => $data['note'] ?? null,
                'snapshot' => $literacySubmission->snapshot(),
            ]);
            ActivityLog::record($data['action'] === 'approve' ? 'Menyetujui hasil literasi' : 'Meminta perbaikan hasil literasi', $literacySubmission->student->name.' · '.$literacySubmission->assignment->title);
        });

        return back()->with('success', $data['action'] === 'approve' ? 'Hasil literasi disetujui.' : 'Hasil dikembalikan untuk diperbaiki.');
    }

    public function attachment(Request $request, LiteracySubmission $literacySubmission): BinaryFileResponse
    {
        $literacySubmission = LiteracySubmission::visibleTo($request->user())->findOrFail($literacySubmission->id);
        abort_unless($literacySubmission->work_path && Storage::disk('local')->exists($literacySubmission->work_path), 404);

        $response = response()->download(Storage::disk('local')->path($literacySubmission->work_path), $literacySubmission->work_original_name, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
        $response->setPrivate();
        $response->headers->set('Cache-Control', 'private, no-store');

        return $response;
    }
}
