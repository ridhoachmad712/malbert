<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate(['q' => 'nullable|string|max:150', 'type' => 'nullable|in:kegiatan,karya,prestasi', 'class' => 'nullable|integer|between:1,6', 'year' => 'nullable|regex:/^20\d{2}\/20\d{2}$/']);
        $query = Publication::where('status', 'published')->whereNotNull('published_content');
        $counts = (clone $query)->get(['published_content'])->countBy(fn ($item) => $item->published_content['type']);
        $years = (clone $query)->get(['published_content'])->pluck('published_content.school_year')->unique()->sortDesc()->values();
        $featured = (clone $query)->where('featured', true)->latest('published_at')->first();
        if ($request->filled('q')) {
            $term = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $request->string('q')->toString()).'%';
            $query->where(fn ($q) => $q->where('published_content->title', 'like', $term)->orWhere('published_content->summary', 'like', $term));
        }
        if ($request->filled('type')) {
            $query->where('published_content->type', $request->input('type'));
        }
        if ($request->filled('class')) {
            $query->whereJsonContains('published_content->classes', (string) $request->input('class'));
        }
        if ($request->filled('year')) {
            $query->where('published_content->school_year', $request->input('year'));
        }

        return view('public.home', ['publications' => $query->latest('published_at')->paginate(9)->withQueryString(), 'counts' => $counts, 'years' => $years, 'featured' => $featured]);
    }

    public function show(string $slug): View
    {
        $publication = Publication::where('slug', $slug)->where('status', 'published')->whereNotNull('published_content')->firstOrFail();
        $related = Publication::where('status', 'published')->where('id', '!=', $publication->id)->where('published_content->type', $publication->published_content['type'])->latest('published_at')->limit(3)->get();

        return view('public.detail', ['publication' => $publication, 'data' => $publication->published_content, 'related' => $related, 'preview' => false]);
    }

    public function profile(): View
    {
        return view('public.profile');
    }

    public function logo(): BinaryFileResponse
    {
        $path = SchoolSetting::values()['logo'] ?? null;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path), ['Cache-Control' => 'no-cache', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function appearanceAsset(string $slot): BinaryFileResponse
    {
        $key = match ($slot) {
            'hero' => 'hero_image',
            'login' => 'login_image',
            'profile' => 'profile_image',
            'favicon' => 'favicon',
            default => null,
        };
        abort_unless($key, 404);
        $path = SchoolSetting::appearance()[$key];
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path), ['Cache-Control' => 'no-cache', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function media(Request $request, Publication $publication, string $filename): BinaryFileResponse
    {
        $path = 'media/'.$filename;
        $public = $publication->status === 'published' ? $publication->published_content : [];
        $paths = array_merge([$public['cover'] ?? null], array_column($public['media'] ?? [], 'path'));
        $isPublic = in_array($path, $paths, true);
        if (! $isPublic && $request->user()?->active && $publication->canEdit($request->user())) {
            $paths = array_merge($paths, [$publication->content['cover'] ?? null], array_column($publication->content['media'] ?? [], 'path'));
        }
        abort_unless(in_array($path, $paths, true) && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path), ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function work(Request $request, Publication $publication, string $filename): BinaryFileResponse
    {
        $path = 'publication-assets/'.$filename;
        $public = $publication->status === 'published' ? $publication->published_content : [];
        $paths = [$public['selected_work']['path'] ?? null];
        if (! in_array($path, $paths, true) && $request->user()?->active && $publication->canEdit($request->user())) {
            $paths[] = $publication->content['selected_work']['path'] ?? null;
        }
        abort_unless(in_array($path, $paths, true) && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path), ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }
}
