@extends('layouts.app')
@section('title', $school['name'])
@section('content')
<div class="container profile-page portfolio-profile">
    <header class="portfolio-profile-intro">
        <span class="eyebrow">TENTANG SEKOLAH</span>
        <h1>{{ $school['name'] }}</h1>
        <p>{{ $school['tagline'] }}</p>
    </header>
    <section class="portfolio-profile-story" aria-label="Profil sekolah">
        <div class="portfolio-profile-image"><img src="{{ $appearance['profile_image'] ? route('appearance.asset', ['slot' => 'profile']) : asset('images/ruang-cerita.webp') }}" alt="Ilustrasi profil {{ $school['name'] }}"></div>
        <div><span class="eyebrow">CERITA KAMI</span><div class="prose">{{ $school['description'] }}</div><a class="portfolio-text-link" href="{{ route('home') }}#jelajah">Lihat portofolio sekolah <span aria-hidden="true">↗</span></a></div>
    </section>
    <div class="portfolio-profile-principles">
        <section><span class="eyebrow">ARAH SEKOLAH</span><h2>Visi</h2><p class="prose">{{ $school['vision'] ?: 'Informasi visi akan dilengkapi sekolah.' }}</p></section>
        <section><span class="eyebrow">LANGKAH SEKOLAH</span><h2>Misi</h2><p class="prose">{{ $school['mission'] ?: 'Informasi misi akan dilengkapi sekolah.' }}</p></section>
    </div>
    <section class="portfolio-profile-contact" aria-labelledby="contact-title">
        <div><span class="eyebrow">KUNJUNGI KAMI</span><h2 id="contact-title">Temui sekolah kita</h2><p>{{ $school['address'] ?: 'Alamat sekolah belum dilengkapi.' }}</p>@if($school['npsn'])<small>NPSN {{ $school['npsn'] }}</small>@endif</div>
        <div><p>{{ $school['email'] ?: 'Email resmi belum dilengkapi.' }}</p><p>{{ $school['phone'] ?: ($school['contact'] ?: 'Nomor telepon resmi belum dilengkapi.') }}</p></div>
    </section>
</div>
@endsection
