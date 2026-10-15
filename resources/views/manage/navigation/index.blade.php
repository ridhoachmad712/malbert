@extends('layouts.app')
@section('title', 'Menu navigasi')
@section('content')
<div class="page-heading">
    <div><span class="eyebrow">SITUS PUBLIK</span><h1>Menu navigasi</h1><p>Atur tautan yang tampil di bagian atas situs. Angka urutan yang lebih kecil tampil lebih dulu.</p></div>
    <a class="button" href="{{ route('navigation.create') }}">Tambah menu</a>
</div>
<section class="panel">
    <div class="section-heading"><h2>Menu saat ini</h2><a href="{{ route('site-pages.index') }}">Kelola halaman publik</a></div>
    @forelse($items as $item)
        <div class="navigation-admin-row">
            <div><strong>{{ $item->label }}</strong><small>{{ ['internal' => 'Halaman aplikasi', 'page' => 'Halaman buatan', 'file' => 'Berkas', 'external' => 'URL eksternal'][$item->type] ?? $item->type }} · {{ $item->is_visible ? 'Ditampilkan' : 'Disembunyikan' }} · Urutan {{ $item->sort_order }}</small>
                @if($item->type === 'page' && $item->sitePage && ! $item->sitePage->is_published)<small>Halaman belum diterbitkan. Menu belum tampil kepada pengunjung.</small>@endif
                @if($item->type === 'external')<small>{{ $item->target }}</small>@endif
            </div>
            <a class="button outline small" href="{{ route('navigation.edit', $item) }}">Atur menu</a>
        </div>
    @empty
        <p>Belum ada menu. Tambahkan menu pertama untuk menampilkan navigasi situs.</p>
    @endforelse
</section>
@endsection
