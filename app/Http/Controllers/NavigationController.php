<?php

namespace App\Http\Controllers;

use App\Models\NavigationItem;
use App\Models\SitePage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NavigationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('manage.navigation.index', ['items' => NavigationItem::with('sitePage')->orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('manage.navigation.form', ['item' => new NavigationItem(['type' => 'internal', 'is_visible' => true]), 'pages' => SitePage::orderBy('title')->get()]);
    }

    public function edit(Request $request, NavigationItem $navigationItem): View
    {
        $this->authorizeAdmin($request);

        return view('manage.navigation.form', ['item' => $navigationItem, 'pages' => SitePage::orderBy('title')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->save($request, new NavigationItem);
    }

    public function update(Request $request, NavigationItem $navigationItem): RedirectResponse
    {
        return $this->save($request, $navigationItem);
    }

    private function save(Request $request, NavigationItem $item): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'type' => ['required', Rule::in(['internal', 'page', 'file', 'external'])],
            'target' => ['nullable', 'string', 'max:2048'],
            'site_page_id' => ['nullable', 'integer'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,webp', 'max:10240'],
            'sort_order' => ['required', 'integer', 'between:0,9999'],
            'is_visible' => ['required', 'boolean'],
            'open_new_tab' => ['required', 'boolean'],
        ]);

        if ($data['type'] === 'internal' && ! in_array($data['target'] ?? null, ['portfolio', 'home', 'readings.public', 'profile'], true)) {
            throw ValidationException::withMessages(['target' => 'Pilih halaman aplikasi yang tersedia.']);
        }
        if ($data['type'] === 'page' && ! SitePage::whereKey($data['site_page_id'] ?? null)->exists()) {
            throw ValidationException::withMessages(['site_page_id' => 'Pilih halaman yang tersedia.']);
        }
        if ($data['type'] === 'external' && (! filter_var($data['target'] ?? null, FILTER_VALIDATE_URL) || ! in_array(parse_url($data['target'], PHP_URL_SCHEME), ['http', 'https'], true))) {
            throw ValidationException::withMessages(['target' => 'Gunakan URL lengkap yang diawali https:// atau http://.']);
        }
        if ($data['type'] === 'file' && ! $request->hasFile('file') && ! ($item->exists && $item->type === 'file' && $item->file_path)) {
            throw ValidationException::withMessages(['file' => 'Pilih berkas untuk menu ini.']);
        }

        $oldFile = $item->file_path;
        $item->fill([
            'label' => $data['label'],
            'type' => $data['type'],
            'target' => in_array($data['type'], ['internal', 'external'], true) ? $data['target'] : null,
            'site_page_id' => $data['type'] === 'page' ? $data['site_page_id'] : null,
            'file_path' => $data['type'] === 'file' ? $item->file_path : null,
            'file_name' => $data['type'] === 'file' ? $item->file_name : null,
            'sort_order' => $data['sort_order'],
            'is_visible' => $data['is_visible'],
            'open_new_tab' => $data['open_new_tab'],
        ]);
        if ($data['type'] === 'file' && $request->hasFile('file')) {
            $item->file_path = $request->file('file')->store('navigation-files', 'local');
            $item->file_name = $request->file('file')->getClientOriginalName();
        }
        $item->save();
        if ($oldFile && $oldFile !== $item->file_path) {
            Storage::disk('local')->delete($oldFile);
        }

        return redirect()->route('navigation.index')->with('success', 'Menu navigasi berhasil disimpan.');
    }

    public function destroy(Request $request, NavigationItem $navigationItem): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $file = $navigationItem->file_path;
        $navigationItem->delete();
        if ($file) {
            Storage::disk('local')->delete($file);
        }

        return redirect()->route('navigation.index')->with('success', 'Menu navigasi dihapus.');
    }

    public function download(NavigationItem $navigationItem): StreamedResponse
    {
        abort_unless($navigationItem->is_visible && $navigationItem->type === 'file' && $navigationItem->file_path, 404);
        abort_unless(Storage::disk('local')->exists($navigationItem->file_path), 404);

        return Storage::disk('local')->download($navigationItem->file_path, $navigationItem->file_name);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }
}
