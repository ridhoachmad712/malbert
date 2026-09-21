<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveReadingResourceRequest;
use App\Models\ActivityLog;
use App\Models\ReadingResource;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReadingResourceController extends Controller
{
    public function index(): View
    {
        return view('public.readings', [
            'readings' => ReadingResource::where('is_public', true)
                ->whereNotNull('published_at')
                ->latest('published_at')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('manage.literacy.reading', ['reading' => new ReadingResource]);
    }

    public function edit(Request $request, ReadingResource $readingResource): View
    {
        abort_unless($readingResource->canEdit($request->user()), 403);

        return view('manage.literacy.reading', ['reading' => $readingResource]);
    }

    public function save(SaveReadingResourceRequest $request, MediaService $mediaService, ?ReadingResource $readingResource = null): RedirectResponse
    {
        if ($readingResource) {
            abort_unless($readingResource->canEdit($request->user()), 403);
        }
        $data = $request->safe()->only(['title', 'description', 'url', 'grade']);
        $isPublic = $request->user()->role === 'admin' && $request->boolean('is_public');
        $createdPath = $request->hasFile('qr') ? $mediaService->storeImage($request->file('qr'), 1200, 'qr') : null;
        $oldPath = null;
        try {
            $readingResource = DB::transaction(function () use ($readingResource, $request, $data, $isPublic, $createdPath, &$oldPath) {
                if ($readingResource) {
                    $readingResource = ReadingResource::lockForUpdate()->findOrFail($readingResource->id);
                } else {
                    $readingResource = new ReadingResource(['user_id' => $request->user()->id]);
                }
                if ($createdPath) {
                    $oldPath = $readingResource->qr_path;
                    $readingResource->qr_path = $createdPath;
                }
                $readingResource->fill($data);
                if ($request->user()->role === 'admin') {
                    $readingResource->is_public = $isPublic;
                    $readingResource->published_at = $isPublic ? ($readingResource->published_at ?? now()) : null;
                }
                $readingResource->save();
                ActivityLog::record('Menyimpan bahan bacaan', $readingResource->title);

                return $readingResource;
            });
        } catch (\Throwable $exception) {
            if ($createdPath) {
                Storage::disk('local')->delete($createdPath);
            }
            throw $exception;
        }
        if ($oldPath) {
            Storage::disk('local')->delete($oldPath);
        }

        return redirect()->route('readings.edit', $readingResource)->with('success', 'Bahan bacaan berhasil disimpan.');
    }

    public function qr(ReadingResource $readingResource): BinaryFileResponse
    {
        abort_unless($readingResource->is_public && $readingResource->published_at && $readingResource->qr_path, 404);
        abort_unless(Storage::disk('local')->exists($readingResource->qr_path), 404);

        return response()->file(Storage::disk('local')->path($readingResource->qr_path), [
            'Cache-Control' => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
