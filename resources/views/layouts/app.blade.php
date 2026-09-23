<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $appearance['app_name']) · {{ $school['name'] }}</title>
    <meta name="description" content="@yield('description', $appearance['app_name'].' — '.$appearance['app_description'].'.')">
    @if(request()->is('kelola*', 'masuk'))<meta name="robots" content="noindex,nofollow">@endif
    <link rel="icon" href="{{ $appearance['favicon'] ? route('appearance.asset', ['slot' => 'favicon']) : asset('favicon.svg') }}" type="{{ $appearance['favicon'] ? 'image/webp' : 'image/svg+xml' }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>:root{--blue:{{ $appearance['primary_color'] }};--navy:{{ $appearance['heading_color'] }};--ink:{{ $appearance['text_color'] }};--muted:{{ $appearance['muted_color'] }};--green:{{ $appearance['secondary_color'] }};--yellow:{{ $appearance['accent_color'] }};--canvas:{{ $appearance['background_color'] }}}</style>
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body class="{{ request()->is('kelola*') ? 'management' : '' }}">
<a class="skip" href="#main">Lewati ke isi utama</a>
<header class="site-header"><div class="header-inner">
    <a class="brand" href="{{ route('home') }}">@if(!empty($school['logo']))<img class="school-logo" src="{{ route('school.logo') }}" alt="Logo sekolah">@else<span class="brand-icon" aria-hidden="true">✳</span>@endif <span class="brand-name">{{ $appearance['app_name'] }}<small>{{ $school['name'] }}</small></span></a>
    <button type="button" class="menu-toggle" aria-expanded="false" aria-controls="main-nav" aria-label="Buka menu navigasi">☰ <span>Menu</span></button>
    <nav id="main-nav" aria-label="Navigasi utama">
        <a class="{{ request()->routeIs('home') && !request('type') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
        <a class="{{ request('type') === 'kegiatan' ? 'active' : '' }}" href="{{ route('home', ['type'=>'kegiatan']) }}#jelajah">Kegiatan</a>
        <a class="{{ request('type') === 'karya' ? 'active' : '' }}" href="{{ route('home', ['type'=>'karya']) }}#jelajah">Karya Kita</a>
        <a class="{{ request('type') === 'prestasi' ? 'active' : '' }}" href="{{ route('home', ['type'=>'prestasi']) }}#jelajah">Prestasi</a>
        <a class="{{ request()->routeIs('readings.public') ? 'active' : '' }}" href="{{ route('readings.public') }}">Pojok Baca</a>
        <a class="{{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}">Sekolah Kita</a>
    </nav>
    <a class="button small outline header-login" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ auth()->check() ? 'Ruang Kelola' : 'Masuk Pengelola' }} <span aria-hidden="true">↗</span></a>
</div></header>
@if($school['demo'] ?? false)<div class="demo-strip">Ruang pratinjau · Identitas dan cerita di sini adalah contoh.</div>@endif
@if(request()->is('kelola*'))
<div class="manage-shell">
<aside class="sidebar">
    <p class="eyebrow">RUANG PENGELOLA</p>
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'selected' : '' }}">▦ Dasbor & konten</a>
    <a href="{{ route('publications.create') }}">＋ Buat cerita</a>
    <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'selected' : '' }}">Data murid</a>
    <a href="{{ route('literacy.index') }}" class="{{ request()->routeIs('literacy.*', 'readings.*') ? 'selected' : '' }}">Literasi</a>
    <a href="{{ route('evaluation.index') }}" class="{{ request()->routeIs('evaluation.*') ? 'selected' : '' }}">Penilaian & poin</a>
    <a href="{{ route('monitoring.index') }}" class="{{ request()->routeIs('monitoring.*') ? 'selected' : '' }}">Monitoring & laporan</a>
    @if(auth()->user()->role === 'admin')
    <a href="{{ route('foundation.index') }}" class="{{ request()->routeIs('foundation.*', 'classrooms.*') ? 'selected' : '' }}">Kelas & tahun ajaran</a>
    <a href="{{ route('users.index') }}">♧ Pengguna</a>
    <a href="{{ route('settings') }}">⚙ Profil sekolah</a>
    <a href="{{ route('appearance.edit') }}" class="{{ request()->routeIs('appearance.*') ? 'selected' : '' }}">◈ Tampilan & konten</a>
    @endif
    <a href="{{ route('account') }}">◉ Akun saya</a>
    <div class="sidebar-person"><strong>{{ auth()->user()->name }}</strong><small>{{ ucfirst(auth()->user()->role) }}</small><form method="post" action="{{ route('logout') }}">@csrf<button class="text-button">Keluar dari akun ↗</button></form></div>
</aside>
<main id="main" class="manage-main">
@else<main id="main">@endif
@if(session('success'))<div class="alert success" role="status">✓ {{ session('success') }}</div>@endif
@if($errors->any())<div class="alert error" role="alert"><strong>Periksa kembali isian berikut:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main>@if(request()->is('kelola*'))</div>@endif
<footer><div class="container footer-inner"><div><a class="brand" href="{{ route('home') }}"><span class="brand-icon" aria-hidden="true">✳</span><span class="brand-name">{{ $appearance['app_name'] }}</span></a><p>{{ $appearance['app_description'] }}</p></div><div><strong>{{ $school['name'] }}</strong><p>{{ $appearance['footer_tagline'] }}</p><small>© {{ date('Y') }} · Dibuat untuk merayakan proses belajar.</small></div></div></footer>
</body></html>
