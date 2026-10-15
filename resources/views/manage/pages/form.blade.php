@extends('layouts.app')
@section('title', $page->exists ? 'Ubah halaman' : 'Buat halaman')
@section('content')
<div class="page-heading"><div><span class="eyebrow">SITUS PUBLIK</span><h1>{{ $page->exists ? 'Ubah halaman' : 'Buat halaman' }}</h1><p>Isi halaman tampil sebagai teks; pisahkan paragraf dengan baris kosong.</p></div><a href="{{ route('site-pages.index') }}">Kembali ke daftar</a></div>
<section class="panel form-panel navigation-form-panel"><form method="post" action="{{ $page->exists ? route('site-pages.update', $page) : route('site-pages.store') }}" data-dirty-form>
    @csrf @if($page->exists) @method('put') @endif
    <label>Judul halaman<input name="title" value="{{ old('title', $page->title) }}" maxlength="150" required></label>
    <label>Alamat halaman<input name="slug" value="{{ old('slug', $page->slug) }}" maxlength="160" pattern="[a-z0-9]+(-[a-z0-9]+)*" required placeholder="contoh: kalender-sekolah"><small>Gunakan huruf kecil, angka, dan tanda hubung. Mengubah alamat dapat memutus tautan lama.</small></label>
    <label>Isi halaman<textarea name="body" rows="15" maxlength="50000" required>{{ old('body', $page->body) }}</textarea></label>
    <label>Status<select name="is_published"><option value="0" @selected(old('is_published', $page->is_published) == 0)>Simpan sebagai draf</option><option value="1" @selected(old('is_published', $page->is_published) == 1)>Terbitkan</option></select></label>
    <div class="form-actions"><button class="button" type="submit">Simpan halaman</button><a class="button outline" href="{{ route('site-pages.index') }}">Batal</a>@if($page->is_published)<a class="button outline" href="{{ route('site-pages.show', $page->slug) }}" target="_blank" rel="noopener">Lihat halaman</a>@endif</div>
</form></section>
@if($page->exists)<form method="post" action="{{ route('site-pages.destroy', $page) }}" onsubmit="return confirm('Hapus halaman ini? Menu terkait akan disembunyikan.')">@csrf @method('delete')<button class="button danger" type="submit">Hapus halaman</button></form>@endif
@endsection
