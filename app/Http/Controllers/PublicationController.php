<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavePublicationRequest;
use App\Models\ActivityLog;
use App\Models\LiteracySubmission;
use App\Models\Publication;
use App\Services\DriveAlbumService;
use App\Services\MediaService;
use App\Services\WorkPublicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicationController extends Controller
{
    public function bank(Request $request): View
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'type' => ['nullable', 'in:text,link,photo,pdf'],
        ]);
        $query = LiteracySubmission::visibleTo($request->user())
            ->where('status', 'approved')
            ->whereIn('work_type', ['text', 'link', 'photo', 'pdf'])
            ->with(['student.classroom', 'assignment', 'publication']);
        if ($request->filled('q')) {
            $term = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $request->string('q')->toString()).'%';
            $query->where(function ($builder) use ($term): void {
                $builder->whereHas('student', fn ($students) => $students->where('name', 'like', $term))
                    ->orWhereHas('assignment', fn ($assignments) => $assignments->where('title', 'like', $term));
            });
        }
        if ($request->filled('type')) {
            $query->where('work_type', $request->input('type'));
        }

        return view('manage.publications.bank', ['submissions' => $query->latest('reviewed_at')->paginate(20)->withQueryString()]);
    }

    public function fromWork(Request $request, LiteracySubmission $literacySubmission, WorkPublicationService $service): RedirectResponse
    {
        $literacySubmission = LiteracySubmission::visibleTo($request->user())->findOrFail($literacySubmission->id);
        $publication = $service->createDraft($request->user(), $literacySubmission);
        ActivityLog::record('Menyiapkan publikasi karya', $literacySubmission->student->name.' · '.$literacySubmission->assignment->title);

        return redirect()->route('publications.edit', $publication)->with('success', 'Draf publikasi dibuat dari karya yang disetujui. Periksa isi dan izin sebelum mengajukannya.');
    }

    public function create(): View
    {
        return view('manage.editor', ['publication' => null, 'data' => []]);
    }

    public function edit(Request $request, Publication $publication): View
    {
        abort_unless($publication->canEdit($request->user()), 403);

        return view('manage.editor', ['publication' => $publication->loadMissing('literacySubmission'), 'data' => $publication->content]);
    }

    public function save(SavePublicationRequest $request, MediaService $mediaService, ?Publication $publication = null): RedirectResponse
    {
        if ($publication) {
            abort_unless($publication->canEdit($request->user()), 403);
        }
        $data = $request->safe()->only(['title', 'type', 'summary', 'body', 'event_date', 'school_year', 'achievement']);
        $data['classes'] = array_values(array_unique(array_map('strval', $request->input('classes', []))));
        $data['permission_confirmed'] = true;
        if ($publication?->literacy_submission_id) {
            $creditMode = $request->input('source_credit_mode', 'class');
            $data['selected_work'] = $publication->content['selected_work'];
            $data['source_credit_mode'] = $creditMode;
            $data['source_credit'] = $creditMode === 'student'
                ? $publication->source_snapshot['student_name']
                : 'Siswa '.$publication->source_snapshot['classroom_name'];
        }
        if ($publication?->content['example'] ?? false) {
            $data['example'] = true;
            $data['emoji'] = $publication->content['emoji'] ?? '✳';
        }
        $data['cover'] = $publication?->content['cover'] ?? null;
        $data['media'] = [];
        $data['album'] = $request->filled('drive_folder') ? app(DriveAlbumService::class)->import($request->input('drive_folder')) : null;
        $allowed = collect($publication?->content['media'] ?? [])->where('provider', 'local')->keyBy('path');
        foreach ($request->input('existing', []) as $item) {
            abort_unless($allowed->has($item['path']), 422);
            $data['media'][] = [...$allowed[$item['path']], 'caption' => $item['caption'] ?? ''];
        }
        foreach ($request->input('media', []) as $index => $item) {
            if (! empty($item['url'])) {
                $data['media'][] = [...$mediaService->drive($item['url'], "media.{$index}.url"), 'kind' => $item['kind'], 'caption' => $item['caption'] ?? ''];
            }
        }
        $created = [];
        try {
            if ($request->hasFile('cover')) {
                $data['cover'] = $created[] = $mediaService->storeImage($request->file('cover'), 1200);
            }
            foreach ($request->file('photos', []) as $file) {
                $path = $created[] = $mediaService->storeImage($file);
                $data['media'][] = ['provider' => 'local', 'path' => $path, 'kind' => 'photo', 'caption' => ''];
            }
            $publication = DB::transaction(function () use ($publication, $request, $data) {
                if ($publication) {
                    $publication = Publication::lockForUpdate()->findOrFail($publication->id);
                } else {
                    $publication = new Publication(['user_id' => $request->user()->id, 'slug' => Str::slug($data['title']).'-'.Str::lower(Str::random(8))]);
                }
                $publication->content = $data;
                $publication->review_status = $request->input('intent') === 'submit' ? 'pending' : 'draft';
                if ($publication->status !== 'published') {
                    $publication->status = $publication->review_status === 'pending' ? 'pending' : 'draft';
                }
                $publication->review_note = null;
                $publication->save();
                ActivityLog::record($request->input('intent') === 'submit' ? 'Mengajukan publikasi' : 'Menyimpan draf', $data['title']);

                return $publication;
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($created);
            throw $exception;
        }

        return redirect()->route('publications.edit', $publication)->with('success', $request->input('intent') === 'submit' ? 'Konten diajukan untuk ditinjau admin.' : 'Draf berhasil disimpan.');
    }

    public function preview(Request $request, Publication $publication): View
    {
        abort_unless($publication->canEdit($request->user()), 403);

        return view('public.detail', ['publication' => $publication, 'data' => $publication->content, 'related' => collect(), 'preview' => true]);
    }

    public function review(Request $request, Publication $publication): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate(['action' => 'required|in:publish,return,archive,feature', 'note' => 'nullable|required_if:action,return|string|max:2000', 'media_checked' => 'accepted_if:action,publish']);
        DB::transaction(function () use ($publication, $data) {
            $publication = Publication::lockForUpdate()->findOrFail($publication->id);
            switch ($data['action']) {
                case 'publish':
                    abort_unless($publication->review_status === 'pending', 409, 'Konten harus diajukan terlebih dahulu.');
                    $publication->published_content = $publication->content;
                    $publication->status = 'published';
                    $publication->review_status = 'approved';
                    $publication->published_at ??= now();
                    $publication->review_note = null;
                    break;
                case 'return':
                    abort_unless($publication->review_status === 'pending', 409);
                    $publication->review_status = 'changes';
                    $publication->review_note = $data['note'];
                    if ($publication->status !== 'published') {
                        $publication->status = 'draft';
                    }
                    break;
                case 'archive':
                    $publication->status = 'archived';
                    $publication->review_status = 'draft';
                    $publication->featured = false;
                    break;
                case 'feature':
                    abort_unless($publication->status === 'published', 409);
                    $publication->featured = ! $publication->featured;
                    break;
            }
            $publication->save();
            ActivityLog::record(match ($data['action']) {
                'publish' => 'Menerbitkan konten', 'return' => 'Meminta perbaikan', 'archive' => 'Mengarsipkan konten', 'feature' => 'Mengubah sorotan'
            }, $publication->content['title']);
        });

        return back()->with('success', 'Status konten berhasil diperbarui.');
    }
}
