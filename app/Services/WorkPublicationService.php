<?php

namespace App\Services;

use App\Models\LiteracySubmission;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WorkPublicationService
{
    public function createDraft(User $user, LiteracySubmission $submission): Publication
    {
        $submission->loadMissing(['student.classroom', 'assignment.academicYear']);
        $this->ensureEligible($submission);

        if ($existing = $submission->publication) {
            return $existing;
        }

        $assetPath = $this->copyAsset($submission);

        try {
            return DB::transaction(function () use ($user, $submission, $assetPath): Publication {
                $existing = Publication::where('literacy_submission_id', $submission->id)->first();
                if ($existing) {
                    if ($assetPath) {
                        Storage::disk('local')->delete($assetPath);
                    }

                    return $existing;
                }

                $selectedWork = match ($submission->work_type) {
                    'text' => ['type' => 'text', 'text' => $submission->work_text],
                    'link' => ['type' => 'link', 'url' => $submission->work_url],
                    'photo', 'pdf' => ['type' => $submission->work_type, 'path' => $assetPath, 'name' => $submission->work_original_name],
                };
                $classroom = $submission->student->classroom;
                $content = [
                    'title' => 'Karya dari '.$submission->assignment->title,
                    'type' => 'karya',
                    'summary' => 'Karya siswa '.$classroom->name.' dari kegiatan literasi '.$submission->assignment->title.'.',
                    'body' => 'Karya ini dipilih dari kegiatan literasi '.$submission->assignment->title.'.',
                    'event_date' => $submission->journal_date->format('Y-m-d'),
                    'school_year' => $submission->assignment->academicYear->name,
                    'achievement' => null,
                    'classes' => [(string) $classroom->grade],
                    'permission_confirmed' => false,
                    'cover' => null,
                    'media' => [],
                    'album' => null,
                    'selected_work' => $selectedWork,
                    'source_credit_mode' => 'class',
                    'source_credit' => 'Siswa '.$classroom->name,
                ];

                return Publication::create([
                    'user_id' => $user->id,
                    'literacy_submission_id' => $submission->id,
                    'source_revision_number' => $submission->revision_number,
                    'source_snapshot' => [
                        'student_name' => $submission->student->name,
                        'classroom_name' => $classroom->name,
                        'assignment_title' => $submission->assignment->title,
                        'work_type' => $submission->work_type,
                        'work_original_name' => $submission->work_original_name,
                    ],
                    'slug' => Str::slug($content['title']).'-'.Str::lower(Str::random(8)),
                    'content' => $content,
                    'status' => 'draft',
                    'review_status' => 'draft',
                ]);
            });
        } catch (\Throwable $exception) {
            if ($assetPath) {
                Storage::disk('local')->delete($assetPath);
            }

            throw $exception;
        }
    }

    private function ensureEligible(LiteracySubmission $submission): void
    {
        $hasWork = match ($submission->work_type) {
            'text' => filled($submission->work_text),
            'link' => filled($submission->work_url),
            'photo', 'pdf' => $submission->work_path && Storage::disk('local')->exists($submission->work_path),
            default => false,
        };

        if ($submission->status !== 'approved' || ! $hasWork) {
            throw ValidationException::withMessages(['work' => 'Hanya karya yang sudah disetujui dan memiliki isi lengkap yang dapat disiapkan untuk publikasi.']);
        }
    }

    private function copyAsset(LiteracySubmission $submission): ?string
    {
        if (! in_array($submission->work_type, ['photo', 'pdf'], true)) {
            return null;
        }

        $extension = strtolower(pathinfo($submission->work_path, PATHINFO_EXTENSION));
        $path = 'publication-assets/'.Str::uuid().'.'.$extension;
        if (! Storage::disk('local')->copy($submission->work_path, $path)) {
            throw ValidationException::withMessages(['work' => 'Berkas karya gagal disalin ke draf publikasi.']);
        }

        return $path;
    }
}
