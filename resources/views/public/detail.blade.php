@extends('layouts.app')
@section('title', $data['title'])
@section('description', $data['summary'])
@section('content')
<div class="container article-container">@if($preview)<div class="alert warning">Pratinjau draf · Belum tentu sama dengan versi publik. <a href="{{ route('publications.edit', $publication) }}">Kembali mengedit →</a></div>@endif
<a class="back-link" href="{{ route('home', ['type'=>$data['type']]) }}">← Kembali ke {{ ucfirst($data['type']) }}</a>
<article class="article"><div class="card-meta"><span class="badge {{ $data['type'] }}">{{ ucfirst($data['type']) }}</span><span>{{ \Carbon\Carbon::parse($data['event_date'])->translatedFormat('d F Y') }}</span><span>{{ count($data['classes'] ?? []) ? 'Kelas '.implode(', ', $data['classes']) : 'Seluruh sekolah' }}</span></div><h1>{{ $data['title'] }}</h1><p class="article-lead">{{ $data['summary'] }}</p>
@if($data['example'] ?? false)<p class="example-notice">Ini adalah contoh cerita, bukan dokumentasi sekolah sebenarnya.</p>@endif
@if(!empty($data['cover']))<img class="article-cover" src="{{ $publication->mediaUrl($data['cover']) }}" alt="{{ $data['title'] }}">@endif
<div class="prose">{{ $data['body'] }}</div>
@if(!empty($data['achievement']))<div class="achievement"><span>🏆</span><div><strong>Pencapaian</strong><p>{{ $data['achievement'] }}</p></div></div>@endif
@if(count($data['media'] ?? []))<h2>Potongan cerita kita</h2><div class="media-gallery">@foreach($data['media'] as $media)<figure>@if($media['provider']==='local')<a href="{{ $publication->mediaUrl($media['path']) }}" target="_blank" rel="noopener"><img src="{{ $publication->mediaUrl($media['path']) }}" alt="{{ $media['caption'] ?: 'Dokumentasi '.$data['title'] }}" loading="lazy"></a>@else<div class="drive-media"><button type="button" class="load-drive" data-src="{{ $media['embed'] }}" data-title="{{ $media['caption'] ?: $data['title'] }}"><span>{{ $media['kind']==='video' ? '▶' : '▧' }}</span>{{ $media['kind']==='video' ? 'Putar video' : 'Lihat foto' }}</button></div><small>Media tidak tampil? <a href="{{ $media['url'] }}" target="_blank" rel="noopener noreferrer">Buka di Google Drive ↗</a></small>@endif @if(!empty($media['caption']))<figcaption>{{ $media['caption'] }}</figcaption>@endif</figure>@endforeach</div>@endif
@include('components.album')
<div class="article-end"><span>✳</span><p>Terima kasih sudah ikut merayakan<br>proses belajar teman-teman kita.</p></div></article>
@if($related->count())<div class="section-heading"><h2>Cerita lainnya</h2><a href="{{ route('home') }}#jelajah">Lihat semua ↗</a></div><div class="story-grid">@foreach($related as $publication)@include('components.card')@endforeach</div>@endif</div>
@endsection
