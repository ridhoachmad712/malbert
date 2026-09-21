@extends('layouts.app')
@section('title', 'Pojok Baca')
@section('description', 'Pilihan bahan bacaan aman yang telah diperiksa sekolah.')
@section('content')
<section class="reading-hero"><div class="container"><span class="eyebrow">POJOK BACA</span><h1>Buka buku, temukan dunia baru.</h1><p>Pilih bacaan yang sudah diperiksa sekolah. Baca bersama pendamping, lalu ceritakan hal yang paling kamu sukai.</p></div></section>
<section class="container reading-section" aria-labelledby="reading-list-title"><div class="section-heading"><div><span class="eyebrow">PILIH BACAAN</span><h2 id="reading-list-title">Rak bacaan digital</h2></div><span class="reading-count">{{ $readings->total() }} bacaan</span></div>
<div class="reading-grid">@forelse($readings as $reading)<article class="reading-card"><div class="reading-card-top"><span class="reading-number">{{ str_pad((string) (($readings->firstItem() ?? 1) + $loop->index), 2, '0', STR_PAD_LEFT) }}</span>@if($reading->grade)<span class="badge kegiatan">Kelas {{ $reading->grade }}</span>@else<span class="badge karya">Semua kelas</span>@endif</div><h3>{{ $reading->title }}</h3><p>{{ $reading->description ?: 'Bacaan pilihan untuk menemani waktu membaca.' }}</p><div class="reading-actions"><a class="button" href="{{ $reading->url }}" target="_blank" rel="noopener noreferrer">Buka bacaan ↗</a>@if($reading->qr_path)<a class="qr-link" href="{{ route('readings.qr', $reading) }}" target="_blank">Lihat QR</a>@endif</div></article>@empty<div class="empty-state"><h3>Rak bacaan sedang disiapkan</h3><p>Pengelola sekolah akan menambahkan bahan bacaan yang sudah diperiksa.</p></div>@endforelse</div>
{{ $readings->links() }}</section>
@endsection
