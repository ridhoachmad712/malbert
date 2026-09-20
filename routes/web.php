<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\PublicController;
use App\Http\Middleware\EnsureActiveUser;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/cerita/{slug}', [PublicController::class, 'show'])->name('story');
Route::get('/profil-sekolah', [PublicController::class, 'profile'])->name('profile');
Route::get('/logo-sekolah', [PublicController::class, 'logo'])->name('school.logo');
Route::get('/media/{publication}/{filename}', [PublicController::class, 'media'])->where('filename', '[a-zA-Z0-9-]+\.webp')->name('media');
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'create'])->name('login');
    Route::post('/masuk', [AuthController::class, 'store'])->middleware('throttle:15,1')->name('login.store');
});
Route::middleware(['auth', EnsureActiveUser::class])->group(function () {
    Route::post('/keluar', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/kelola', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/kelola/konten/baru', [PublicationController::class, 'create'])->name('publications.create');
    Route::post('/kelola/konten', [PublicationController::class, 'save'])->name('publications.store');
    Route::get('/kelola/konten/{publication}', [PublicationController::class, 'edit'])->name('publications.edit');
    Route::put('/kelola/konten/{publication}', [PublicationController::class, 'save'])->name('publications.update');
    Route::get('/kelola/konten/{publication}/pratinjau', [PublicationController::class, 'preview'])->name('publications.preview');
    Route::post('/kelola/konten/{publication}/tinjauan', [PublicationController::class, 'review'])->name('publications.review');
    Route::get('/kelola/pengguna', [AdminController::class, 'users'])->name('users.index');
    Route::post('/kelola/pengguna', [AdminController::class, 'saveUser'])->name('users.store');
    Route::put('/kelola/pengguna/{user}', [AdminController::class, 'saveUser'])->name('users.update');
    Route::get('/kelola/sekolah', [AdminController::class, 'settings'])->name('settings');
    Route::put('/kelola/sekolah', [AdminController::class, 'saveSettings'])->name('settings.update');
    Route::view('/kelola/akun', 'manage.account')->name('account');
    Route::put('/kelola/akun', [AuthController::class, 'password'])->name('password.update');
});
