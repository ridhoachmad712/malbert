@extends('layouts.app')
@section('title', 'Halaman publik')
@section('content')
<div class="page-heading"><div><span class="eyebrow">SITUS PUBLIK</span><h1>Halaman publik</h1><p>Tulis informasi sekolah yang dapat dibuka melalui menu navigasi.</p></div><a class="button" href="{{ route('site-pages.create') }}">Buat halaman</a></div>
<section class="panel"><div class="section-heading"><h2>Daftar halaman</h2><a href="{{ route('navigation.index') }}">Kelola menu navigasi</a></div>
    @forelse($pages as $page)
        <div class="navigation-admin-row"><div><strong>{{ $page->title }}</strong><small>{{ $page->is_published ? 'Diterbitkan' : 'Draf' }} · /halaman/{{ $page->slug }}</small></div><a class="button outline small" href="{{ route('site-pages.edit', $page) }}">Ubah halaman</a></div>
    @empty
        <p>Belum ada halaman. Buat halaman informasi sekolah untuk ditautkan dari menu.</p>
    @endforelse
</section>
@endsection
