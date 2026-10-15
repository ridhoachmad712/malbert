@extends('layouts.app')
@section('title', $item->exists ? 'Atur menu' : 'Tambah menu')
@section('content')
<div class="page-heading"><div><span class="eyebrow">SITUS PUBLIK</span><h1>{{ $item->exists ? 'Atur menu' : 'Tambah menu' }}</h1><p>Setiap menu memiliki satu tujuan. Pilih jenis yang sesuai dengan isi yang ingin dibuka pengunjung.</p></div><a href="{{ route('navigation.index') }}">Kembali ke daftar</a></div>
<section class="panel form-panel navigation-form-panel">
    <form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('navigation.update', $item) : route('navigation.store') }}" data-dirty-form>
        @csrf
        @if($item->exists) @method('put') @endif
        <div class="two-col">
            <label>Nama menu<input name="label" value="{{ old('label', $item->label) }}" maxlength="80" required placeholder="Contoh: Kalender sekolah"></label>
            <label>Jenis tujuan<select name="type" id="navigation-type" required>
                @foreach(['internal' => 'Halaman aplikasi', 'page' => 'Halaman buatan admin', 'file' => 'Berkas unduhan', 'external' => 'URL eksternal'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('type', $item->type) === $value)>{{ $label }}</option>
                @endforeach
            </select></label>
        </div>
        <div class="navigation-target" data-target-type="internal">
            <label>Halaman aplikasi<select name="target_internal">
                @foreach(['portfolio' => 'Portofolio', 'home' => 'Beranda', 'readings.public' => 'Pojok Baca', 'profile' => 'Tentang Sekolah'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('target', $item->target) === $value)>{{ $label }}</option>
                @endforeach
            </select></label>
        </div>
        <div class="navigation-target" data-target-type="page">
            <label>Halaman buatan<select name="site_page_id">
                <option value="">Pilih halaman</option>
                @foreach($pages as $page)
                    <option value="{{ $page->id }}" @selected(old('site_page_id', $item->site_page_id) == $page->id)>{{ $page->title }}{{ $page->is_published ? '' : ' (draf)' }}</option>
                @endforeach
            </select></label>
            <p class="form-help">Belum ada halaman? <a href="{{ route('site-pages.create') }}">Buat halaman baru</a>. Menu draf belum terlihat oleh pengunjung.</p>
        </div>
        <div class="navigation-target" data-target-type="file">
            <label>{{ $item->file_path ? 'Ganti berkas' : 'Unggah berkas' }}<input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.webp"></label>
            <p class="form-help">PDF, dokumen Office, atau gambar; maksimal 10 MB. Jangan unggah data pribadi murid.{{ $item->file_name ? ' Berkas saat ini: '.$item->file_name.'.' : '' }}</p>
        </div>
        <div class="navigation-target" data-target-type="external">
            <label>URL lengkap<input name="target_external" type="url" value="{{ old('target', $item->type === 'external' ? $item->target : '') }}" placeholder="https://contoh.sch.id"></label>
            <p class="form-help">Gunakan alamat https:// atau http://. Tautan luar sebaiknya dibuka di tab baru.</p>
        </div>
        <div class="two-col">
            <label>Urutan<input type="number" name="sort_order" min="0" max="9999" value="{{ old('sort_order', $item->sort_order ?? 40) }}" required><small>Contoh: 10 tampil sebelum 20.</small></label>
            <label>Status<select name="is_visible"><option value="1" @selected(old('is_visible', $item->is_visible) == 1)>Tampilkan</option><option value="0" @selected(old('is_visible', $item->is_visible) == 0)>Sembunyikan</option></select></label>
        </div>
        <label class="check-label"><input type="hidden" name="open_new_tab" value="0"><input type="checkbox" name="open_new_tab" value="1" @checked(old('open_new_tab', $item->open_new_tab))><span>Buka di tab baru</span></label>
        <div class="form-actions"><button class="button" type="submit">Simpan menu</button><a class="button outline" href="{{ route('navigation.index') }}">Batal</a></div>
    </form>
</section>
@if($item->exists)
<form method="post" action="{{ route('navigation.destroy', $item) }}" onsubmit="return confirm('Hapus menu ini dari situs?')">@csrf @method('delete')<button class="button danger" type="submit">Hapus menu</button></form>
@endif
<script>
document.addEventListener('DOMContentLoaded', () => {
    const type = document.getElementById('navigation-type');
    const sections = document.querySelectorAll('[data-target-type]');
    function updateType() {
        sections.forEach(section => {
            const selected = section.dataset.targetType === type.value;
            section.hidden = !selected;
            section.querySelectorAll('input, select').forEach(field => { field.disabled = !selected; });
        });
    }
    type.addEventListener('change', updateType);
    updateType();
    type.form.addEventListener('submit', () => {
        const field = document.querySelector('[data-target-type="' + type.value + '"] [name="target_' + type.value + '"]');
        if (field) {
            field.name = 'target';
        }
    });
});
</script>
@endsection
