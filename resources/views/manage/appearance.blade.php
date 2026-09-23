@extends('layouts.app')
@section('title', 'Tampilan & konten')
@section('content')
<div class="page-heading">
    <div>
        <span class="eyebrow">TAMPILAN & KONTEN</span>
        <h1>Atur wajah aplikasi</h1>
        <p>Sesuaikan identitas, warna, teks utama, dan gambar publik tanpa mengubah kode.</p>
    </div>
    <a class="button outline" href="{{ route('home') }}" target="_blank" rel="noopener">Buka halaman publik ↗</a>
</div>

<form method="post" enctype="multipart/form-data" action="{{ route('appearance.update') }}" data-theme-form data-dirty-form data-submit-label="Menyimpan tampilan…">
    @csrf
    @method('put')
    <div class="appearance-layout">
        <div>
            <section class="panel form-panel">
                <h2>Identitas aplikasi</h2>
                <div class="two-col">
                    <label>Nama aplikasi
                        <input name="app_name" value="{{ old('app_name', $appearance['app_name']) }}" maxlength="50" required data-preview-text>
                    </label>
                    <label>Nama penjelas
                        <input name="app_description" value="{{ old('app_description', $appearance['app_description']) }}" maxlength="160" required data-preview-text>
                    </label>
                </div>
                <p class="form-help">Nama sekolah tetap diatur terpisah melalui menu Profil sekolah.</p>
            </section>

            <section class="panel form-panel">
                <h2>Warna</h2>
                <p class="muted">Sistem akan menolak kombinasi warna teks yang sulit dibaca.</p>
                <div class="color-field-grid">
                    @foreach([
                        'primary_color' => ['Warna utama', '--preview-primary'],
                        'heading_color' => ['Judul', '--preview-heading'],
                        'text_color' => ['Teks', '--preview-text'],
                        'muted_color' => ['Teks sekunder', '--preview-muted'],
                        'secondary_color' => ['Aksen hijau', '--preview-secondary'],
                        'accent_color' => ['Aksen terang', '--preview-accent'],
                        'background_color' => ['Latar', '--preview-background'],
                    ] as $key => [$label, $variable])
                        <label>{{ $label }}
                            <span class="color-input"><input type="color" name="{{ $key }}" value="{{ old($key, $appearance[$key]) }}" data-preview-color="{{ $variable }}"><code data-color-value>{{ old($key, $appearance[$key]) }}</code></span>
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="panel form-panel">
                <h2>Halaman utama</h2>
                <div class="two-col">
                    <label>Judul pembuka<input name="hero_title" value="{{ old('hero_title', $appearance['hero_title']) }}" maxlength="80" required data-preview-text></label>
                    <label>Teks sorotan<input name="hero_highlight" value="{{ old('hero_highlight', $appearance['hero_highlight']) }}" maxlength="80" required data-preview-text></label>
                </div>
                <label>Deskripsi<textarea name="hero_description" rows="3" maxlength="320" required data-preview-text>{{ old('hero_description', $appearance['hero_description']) }}</textarea></label>
                <div class="two-col">
                    <label>Label tombol<input name="hero_button_label" value="{{ old('hero_button_label', $appearance['hero_button_label']) }}" maxlength="40" required data-preview-text></label>
                    <label>Catatan singkat<input name="hero_footnote" value="{{ old('hero_footnote', $appearance['hero_footnote']) }}" maxlength="120" required data-preview-text></label>
                </div>
            </section>

            <section class="panel form-panel">
                <h2>Halaman masuk</h2>
                <label>Label bagian<input name="login_label" value="{{ old('login_label', $appearance['login_label']) }}" maxlength="80" required></label>
                <label>Judul<input name="login_title" value="{{ old('login_title', $appearance['login_title']) }}" maxlength="120" required></label>
                <label>Deskripsi<textarea name="login_description" rows="3" maxlength="320" required>{{ old('login_description', $appearance['login_description']) }}</textarea></label>
                <label>Tagline footer<input name="footer_tagline" value="{{ old('footer_tagline', $appearance['footer_tagline']) }}" maxlength="160" required data-preview-text></label>
            </section>

            <section class="panel form-panel">
                <h2>Gambar publik</h2>
                <p class="muted">JPG, PNG, atau WebP. Gambar otomatis diperkecil dan disimpan sebagai WebP.</p>
                <div class="appearance-media-grid">
                    @foreach([
                        'hero_image' => ['Gambar halaman utama', 'hero', 'Maksimal 10 MB'],
                        'login_image' => ['Gambar halaman masuk', 'login', 'Maksimal 10 MB'],
                        'profile_image' => ['Gambar profil sekolah', 'profile', 'Maksimal 10 MB'],
                        'favicon' => ['Ikon browser', 'favicon', 'Maksimal 2 MB'],
                    ] as $key => [$label, $slot, $help])
                        <div class="appearance-media-field">
                            <strong>{{ $label }}</strong>
                            @if($appearance[$key])
                                <img src="{{ route('appearance.asset', ['slot' => $slot]) }}" alt="{{ $label }} saat ini">
                            @endif
                            <label>Ganti gambar<input type="file" name="{{ $key }}_upload" accept="image/jpeg,image/png,image/webp" @if($key === 'hero_image') data-preview-file @endif><small>{{ $help }}. Kosongkan untuk mempertahankan gambar saat ini.</small></label>
                            @if($appearance[$key])
                                <label class="check-label"><input type="checkbox" name="remove_{{ $key }}" value="1"><span>Gunakan gambar bawaan</span></label>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <aside class="appearance-preview-column">
            <section class="theme-preview" data-theme-preview style="--preview-primary:{{ old('primary_color', $appearance['primary_color']) }};--preview-heading:{{ old('heading_color', $appearance['heading_color']) }};--preview-text:{{ old('text_color', $appearance['text_color']) }};--preview-muted:{{ old('muted_color', $appearance['muted_color']) }};--preview-secondary:{{ old('secondary_color', $appearance['secondary_color']) }};--preview-accent:{{ old('accent_color', $appearance['accent_color']) }};--preview-background:{{ old('background_color', $appearance['background_color']) }}">
                <div class="theme-preview-header"><span class="theme-preview-mark">✳</span><strong data-preview-value="app_name">{{ old('app_name', $appearance['app_name']) }}</strong></div>
                <div class="theme-preview-body">
                    <small><span data-preview-value="app_name">{{ old('app_name', $appearance['app_name']) }}</span> · <span data-preview-value="app_description">{{ old('app_description', $appearance['app_description']) }}</span></small>
                    <h2><span data-preview-value="hero_title">{{ old('hero_title', $appearance['hero_title']) }}</span> <em data-preview-value="hero_highlight">{{ old('hero_highlight', $appearance['hero_highlight']) }}</em></h2>
                    <p data-preview-value="hero_description">{{ old('hero_description', $appearance['hero_description']) }}</p>
                    <span class="theme-preview-button" data-preview-value="hero_button_label">{{ old('hero_button_label', $appearance['hero_button_label']) }}</span>
                    <p class="theme-preview-note" data-preview-value="hero_footnote">{{ old('hero_footnote', $appearance['hero_footnote']) }}</p>
                    <img data-preview-image src="{{ $appearance['hero_image'] ? route('appearance.asset', ['slot' => 'hero']) : asset('images/ruang-cerita.webp') }}" alt="Pratinjau gambar utama">
                </div>
                <div class="theme-preview-footer"><strong data-preview-value="app_name">{{ old('app_name', $appearance['app_name']) }}</strong><span data-preview-value="footer_tagline">{{ old('footer_tagline', $appearance['footer_tagline']) }}</span></div>
            </section>
            <p class="preview-caption">Pratinjau memperlihatkan arah warna dan konten utama. Buka halaman publik setelah menyimpan untuk memeriksa seluruh halaman.</p>
            <button class="button full">Simpan tampilan & konten</button>
        </aside>
    </div>
</form>

<form method="post" action="{{ route('appearance.reset') }}" class="panel danger-zone" data-confirm="Semua warna, teks, dan gambar khusus akan dikembalikan ke bawaan AksiKita. Lanjutkan?">
    @csrf
    @method('delete')
    <div><h2>Kembalikan tampilan bawaan</h2><p>Identitas sekolah, publikasi, akun, dan data siswa tidak berubah.</p></div>
    <button class="button danger">Kembalikan bawaan</button>
</form>
@endsection
