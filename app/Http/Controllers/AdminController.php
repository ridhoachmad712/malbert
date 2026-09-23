<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SchoolSetting;
use App\Models\User;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function users(Request $request): View
    {
        abort_unless($request->user()->role === 'admin', 403);

        return view('manage.users', ['users' => User::orderBy('name')->get()]);
    }

    public function saveUser(Request $request, ?User $user = null): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate(['name' => 'required|string|max:120', 'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($user?->id)], 'password' => [$user ? 'nullable' : 'required', 'string', 'min:12'], 'role' => 'required|in:admin,guru', 'active' => 'required|boolean']);
        if ($user?->id === $request->user()->id && ($data['role'] !== 'admin' || ! $data['active'])) {
            return back()->withErrors(['active' => 'Anda tidak dapat menonaktifkan atau menurunkan peran akun sendiri.']);
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }
        if ($user) {
            $user->update($data);
        } else {
            $user = User::create($data);
        }
        ActivityLog::record('Memperbarui akun', $user->name);

        return back()->with('success', 'Akun berhasil disimpan.');
    }

    public function settings(Request $request): View
    {
        abort_unless($request->user()->role === 'admin', 403);

        return view('manage.settings');
    }

    public function saveSettings(Request $request, MediaService $mediaService): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'npsn' => ['nullable', 'digits:8'],
            'tagline' => 'required|string|max:200',
            'description' => 'nullable|string|max:10000',
            'vision' => 'nullable|string|max:5000',
            'mission' => 'nullable|string|max:5000',
            'address' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:190',
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+().\-\s]+$/'],
            'demo' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240|dimensions:max_width=8000,max_height=8000',
        ]);
        $data['logo'] = $request->hasFile('logo') ? $mediaService->storeImage($request->file('logo'), 320) : (SchoolSetting::values()['logo'] ?? null);
        $data['demo'] = $request->boolean('demo');
        $setting = SchoolSetting::firstOrNew(['id' => 1]);
        $setting->data = array_replace($setting->data ?? [], $data);
        $setting->save();
        ActivityLog::record('Memperbarui profil sekolah', $data['name']);

        return back()->with('success', 'Profil sekolah berhasil diperbarui.');
    }
}
