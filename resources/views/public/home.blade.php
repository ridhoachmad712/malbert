@extends('layouts.app')
@section('content')
<div class="container portfolio-home">
@unless(request()->hasAny(['q', 'type', 'class', 'year', 'page']))
<section class="portfolio-intro" aria-labelledby="portfolio-title">
    <div class="portfolio-intro-copy">
        <span class="eyebrow">{{ $school['name'] }} / {{ $appearance['app_description'] }}</span>
        <h1 id="portfolio-title">{{ $appearance['hero_title'] }} <span>{{ $appearance['hero_highlight'] }}</span></h1>
        <p>{{ $appearance['hero_description'] }}</p>
        <a class="portfolio-text-link" href="#jelajah">{{ $appearance['hero_button_label'] }} <span aria-hidden="true">↘</span></a>
        <span class="portfolio-intro-note">{{ $appearance['hero_footnote'] }}</span>
    </div>
    <div class="portfolio-intro-visual">
        @if($featured)
            @php($selected = $featured->published_content)
            <a class="portfolio-feature" href="{{ route('story', $featured->slug) }}" aria-label="Lihat cerita pilihan: {{ $selected['title'] }}">
                <span class="portfolio-feature-image cover-{{ $selected['type'] }}">
                    @if(!empty($selected['cover']))
                        <img src="{{ $featured->mediaUrl($selected['cover']) }}" alt="" width="900" height="720" fetchpriority="high">
                    @else
                        <span class="portfolio-feature-placeholder" aria-hidden="true">{{ $selected['emoji'] ?? ['kegiatan'=>'🌱', 'karya'=>'🎨', 'prestasi'=>'🏆'][$selected['type']] }}</span>
                    @endif
                </span>
                <span class="portfolio-feature-caption"><small>Cerita pilihan sekolah · {{ ucfirst($selected['type']) }}@if($selected['example'] ?? false) · Contoh cerita @endif</small><strong>{{ $selected['title'] }}</strong><span aria-hidden="true">↗</span></span>
            </a>
        @else
            <div class="portfolio-intro-image"><img src="{{ $appearance['hero_image'] ? route('appearance.asset', ['slot' => 'hero']) : asset('images/ruang-cerita.webp') }}" alt="Ilustrasi {{ $appearance['app_name'] }}" width="768" height="512" fetchpriority="high"></div>
        @endif
    </div>
</section>
<nav class="portfolio-categories" aria-label="Jelajahi portofolio">
    <a href="{{ route('home', ['type' => 'karya']) }}#jelajah"><span>01 / Karya</span><strong>Hasil dari tangan dan gagasan siswa</strong><span aria-hidden="true">↗</span></a>
    <a href="{{ route('home', ['type' => 'kegiatan']) }}#jelajah"><span>02 / Kegiatan</span><strong>Proses belajar yang terdokumentasi</strong><span aria-hidden="true">↗</span></a>
    <a href="{{ route('home', ['type' => 'prestasi']) }}#jelajah"><span>03 / Prestasi</span><strong>Usaha dan pencapaian yang dirayakan</strong><span aria-hidden="true">↗</span></a>
</nav>
@endunless
<section class="explore portfolio-explore" id="jelajah" aria-labelledby="portfolio-collection-title">
    <div class="portfolio-collection-heading">
        <div><span class="eyebrow">ARSIP KARYA & CERITA</span><h2 id="portfolio-collection-title">{{ request('type') ? ['kegiatan'=>'Kegiatan', 'karya'=>'Karya siswa', 'prestasi'=>'Prestasi'][request('type')] : 'Pilihan dari sekolah' }}</h2></div>
        <p>{{ $publications->total() }} cerita {{ request()->hasAny(['q', 'type', 'class', 'year']) ? 'sesuai pencarian' : 'tersedia untuk dijelajahi' }}</p>
    </div>
    <form class="filters portfolio-filters" action="{{ route('home') }}#jelajah" method="get" data-submit-label="Mencari…">
        <label class="search-field"><span class="sr-only">Cari cerita</span><input name="q" value="{{ request('q') }}" placeholder="Cari judul atau cerita..." maxlength="150"></label>
        <label><span class="sr-only">Jenis cerita</span><select name="type"><option value="">Semua jenis</option><option value="kegiatan" @selected(request('type') === 'kegiatan')>Kegiatan</option><option value="karya" @selected(request('type') === 'karya')>Karya siswa</option><option value="prestasi" @selected(request('type') === 'prestasi')>Prestasi</option></select></label>
        <label><span class="sr-only">Kelas</span><select name="class"><option value="">Semua kelas</option>@foreach(range(1, 6) as $class)<option value="{{ $class }}" @selected(request('class') == $class)>Kelas {{ $class }}</option>@endforeach</select></label>
        <label><span class="sr-only">Tahun ajaran</span><select name="year"><option value="">Semua tahun</option>@foreach($years as $year)<option @selected(request('year') === $year)>{{ $year }}</option>@endforeach</select></label>
        <button class="button" type="submit">Tampilkan</button>
    </form>
    @if(request()->filled('q') || request()->filled('type') || request()->filled('class') || request()->filled('year'))<p class="filter-reset"><a href="{{ route('home') }}#jelajah">Hapus filter</a></p>@endif
    <div class="story-grid portfolio-grid">@forelse($publications as $publication)@include('components.card')@empty<div class="empty-state"><h3>Belum ada cerita yang cocok</h3><p>Coba kata kunci lain atau lihat seluruh portofolio.</p><a class="button outline" href="{{ route('home') }}#jelajah">Lihat semua cerita</a></div>@endforelse</div>
    {{ $publications->links() }}
</section>
</div>
@endsection
