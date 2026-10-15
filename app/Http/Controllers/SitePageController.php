<?php

namespace App\Http\Controllers;

use App\Models\SitePage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SitePageController extends Controller
{
    public function show(string $slug): View
    {
        $page = SitePage::where('slug', $slug)->where('is_published', true)->firstOrFail();

        return view('public.page', compact('page'));
    }

    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('manage.pages.index', ['pages' => SitePage::orderBy('title')->get()]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('manage.pages.form', ['page' => new SitePage]);
    }

    public function edit(Request $request, SitePage $sitePage): View
    {
        $this->authorizeAdmin($request);

        return view('manage.pages.form', ['page' => $sitePage]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->save($request, new SitePage);
    }

    public function update(Request $request, SitePage $sitePage): RedirectResponse
    {
        return $this->save($request, $sitePage);
    }

    private function save(Request $request, SitePage $page): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('site_pages', 'slug')->ignore($page->id)],
            'body' => ['required', 'string', 'max:50000'],
            'is_published' => ['required', 'boolean'],
        ]);
        $page->fill($data);
        $page->save();

        return redirect()->route('site-pages.index')->with('success', 'Halaman berhasil disimpan.');
    }

    public function destroy(Request $request, SitePage $sitePage): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $sitePage->navigationItems()->update(['is_visible' => false, 'site_page_id' => null]);
        $sitePage->delete();

        return redirect()->route('site-pages.index')->with('success', 'Halaman dihapus. Menu yang mengarah ke halaman ini disembunyikan.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }
}
