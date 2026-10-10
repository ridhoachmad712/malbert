@extends('layouts.app')
@section('title', 'Cerita sekolah')
@section('content')
<div class="page-heading">
    <div><span class="eyebrow">PUBLIKASI SEKOLAH</span><h1>Cerita sekolah</h1><p>Kelola dokumentasi kegiatan, prestasi, dan karya yang dipilih untuk halaman publik.</p></div>
    <div class="heading-actions"><a class="button outline" href="{{ route('publications.bank') }}">Bank karya</a><a class="button" href="{{ route('publications.create') }}">Buat cerita</a></div>
</div>
<section class="panel">
    <form class="filters compact" action="{{ route('publications.index') }}" method="get">
        <label class="search-field"><span aria-hidden="true">⌕</span><span class="sr-only">Cari judul cerita</span><input name="q" value="{{ request('q') }}" placeholder="Cari judul cerita..."></label>
        <label><span class="sr-only">Status cerita</span><select name="status"><option value="">Semua status</option>@foreach(['draft' => 'Draf & perbaikan', 'pending' => 'Menunggu tinjauan', 'published' => 'Terbit', 'archived' => 'Diarsipkan'] as $key => $label)<option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>@endforeach</select></label>
        <button class="button outline">Tampilkan</button>
    </form>
    <div class="publication-list">
        @forelse($publications as $publication)
            <article class="publication-row">
                <div class="publication-main"><a class="table-title" href="{{ route('publications.edit', $publication) }}">{{ $publication->content['title'] }}</a><small>{{ ucfirst($publication->content['type']) }} @if($publication->featured) · Sorotan @endif</small></div>
                <div class="publication-meta"><span>Penulis</span><strong>{{ $publication->author->name }}</strong></div>
                <div class="publication-meta"><span>Status</span><strong><span class="badge {{ $publication->status === 'published' ? 'kegiatan' : 'karya' }}">{{ ['draft' => 'Draf', 'pending' => 'Menunggu', 'published' => 'Terbit', 'archived' => 'Arsip'][$publication->status] }}</span></strong>@if($publication->status === 'published' && $publication->review_status !== 'approved')<small>Revisi: {{ ['draft' => 'draf', 'pending' => 'menunggu', 'changes' => 'perbaikan'][$publication->review_status] }}</small>@elseif($publication->review_status === 'changes')<small>Perlu perbaikan</small>@endif</div>
                <div class="publication-meta"><span>Diperbarui</span><strong>{{ $publication->updated_at->translatedFormat('d M Y') }}</strong></div>
                <a class="button small outline" href="{{ route('publications.edit', $publication) }}">Buka</a>
            </article>
        @empty
            <div class="empty-state"><h3>Belum ada cerita yang cocok</h3><p>Ubah pencarian atau mulai menulis cerita sekolah.</p><a class="button outline" href="{{ route('publications.create') }}">Buat cerita</a></div>
        @endforelse
    </div>
    {{ $publications->links() }}
</section>
@endsection
