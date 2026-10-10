<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $appearance['app_name']) · {{ $school['name'] }}</title>
    <meta name="description" content="@yield('description', $appearance['app_name'].' — '.$appearance['app_description'].'.')">
    @if(request()->is('kelola*', 'masuk'))
        <meta name="robots" content="noindex,nofollow">
    @endif
    <link rel="icon" href="{{ $appearance['favicon'] ? route('appearance.asset', ['slot' => 'favicon']) : asset('favicon.svg') }}" type="{{ $appearance['favicon'] ? 'image/webp' : 'image/svg+xml' }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>:root{--blue:{{ $appearance['primary_color'] }};--navy:{{ $appearance['heading_color'] }};--ink:{{ $appearance['text_color'] }};--muted:{{ $appearance['muted_color'] }};--green:{{ $appearance['secondary_color'] }};--yellow:{{ $appearance['accent_color'] }};--canvas:{{ $appearance['background_color'] }}}</style>
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body class="{{ request()->is('kelola*') ? 'management' : '' }}">
<a class="skip" href="#main">Lewati ke isi utama</a>

@if(request()->is('kelola*'))
    <header class="admin-header">
        <div class="admin-header-inner">
            <a class="admin-brand" href="{{ route('dashboard') }}">
                @if(!empty($school['logo']))
                    <img class="admin-logo" src="{{ route('school.logo') }}" alt="Logo sekolah">
                @else
                    <span class="admin-brand-mark" aria-hidden="true">✳</span>
                @endif
                <span><strong>{{ $appearance['app_name'] }}</strong><small>Ruang pengelola</small></span>
            </a>
            <div class="admin-header-actions">
                @if($school['demo'] ?? false)
                    <span class="admin-environment">Pratinjau</span>
                @endif
                <a class="admin-public-link" href="{{ route('home') }}" target="_blank" rel="noopener">Lihat situs <span aria-hidden="true">↗</span></a>
                <span class="admin-role">{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Guru' }}</span>
                <button type="button" class="admin-menu-toggle" aria-expanded="false" aria-controls="admin-sidebar" aria-label="Buka menu pengelolaan">
                    <span aria-hidden="true">☰</span><span>Menu</span>
                </button>
            </div>
        </div>
    </header>

    <div class="manage-shell">
        <aside id="admin-sidebar" class="sidebar" aria-label="Menu pengelolaan">
            <nav class="sidebar-nav">
                <div class="sidebar-group">
                    <p class="sidebar-label">Ringkasan</p>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'selected' : '' }}">Dasbor</a>
                </div>

                <div class="sidebar-group">
                    <p class="sidebar-label">Konten sekolah</p>
                    <a href="{{ route('publications.create') }}" class="{{ request()->routeIs('publications.create', 'publications.edit', 'publications.preview') ? 'selected' : '' }}">Cerita sekolah</a>
                    <a href="{{ route('publications.bank') }}" class="{{ request()->routeIs('publications.bank') ? 'selected' : '' }}">Bank karya</a>
                </div>

                <div class="sidebar-group">
                    <p class="sidebar-label">Pembelajaran</p>
                    <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'selected' : '' }}">Data murid</a>
                    <a href="{{ route('literacy.index') }}" class="{{ request()->routeIs('literacy.*', 'readings.*') ? 'selected' : '' }}">Literasi</a>
                    <a href="{{ route('evaluation.index') }}" class="{{ request()->routeIs('evaluation.*') ? 'selected' : '' }}">Penilaian & poin</a>
                    <a href="{{ route('monitoring.index') }}" class="{{ request()->routeIs('monitoring.*') ? 'selected' : '' }}">Monitoring & laporan</a>
                </div>

                @if(auth()->user()->role === 'admin')
                    <div class="sidebar-group">
                        <p class="sidebar-label">Administrasi</p>
                        <a href="{{ route('foundation.index') }}" class="{{ request()->routeIs('foundation.*', 'classrooms.*', 'years.*') ? 'selected' : '' }}">Kelas & tahun ajaran</a>
                        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'selected' : '' }}">Pengguna</a>
                        <a href="{{ route('settings') }}" class="{{ request()->routeIs('settings', 'settings.update') ? 'selected' : '' }}">Profil sekolah</a>
                        <a href="{{ route('appearance.edit') }}" class="{{ request()->routeIs('appearance.*') ? 'selected' : '' }}">Tampilan & konten</a>
                    </div>
                @endif

                <div class="sidebar-group sidebar-account-link">
                    <p class="sidebar-label">Akun</p>
                    <a href="{{ route('account') }}" class="{{ request()->routeIs('account', 'password.update') ? 'selected' : '' }}">Pengaturan akun</a>
                </div>
            </nav>

            <div class="sidebar-person">
                <span class="sidebar-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="sidebar-person-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Guru' }}</small></span>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="sidebar-logout" aria-label="Keluar dari akun">Keluar</button>
                </form>
            </div>
        </aside>

        <main id="main" class="manage-main">
            @if(session('success'))
                <div class="alert success" role="status">✓ {{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert error" role="alert"><strong>Periksa kembali isian berikut:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @yield('content')
        </main>
    </div>
@else
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="{{ route('home') }}">
                @if(!empty($school['logo']))
                    <img class="school-logo" src="{{ route('school.logo') }}" alt="Logo sekolah">
                @else
                    <span class="brand-icon" aria-hidden="true">✳</span>
                @endif
                <span class="brand-name">{{ $appearance['app_name'] }}<small>{{ $school['name'] }}</small></span>
            </a>
            <button type="button" class="menu-toggle" aria-expanded="false" aria-controls="main-nav" aria-label="Buka menu navigasi">☰ <span>Menu</span></button>
            <nav id="main-nav" aria-label="Navigasi utama">
                <a class="{{ request()->routeIs('home', 'story') ? 'active' : '' }}" href="{{ route('home') }}#jelajah">Portofolio</a>
                <a class="{{ request()->routeIs('readings.public') ? 'active' : '' }}" href="{{ route('readings.public') }}">Pojok Baca</a>
                <a class="{{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}">Tentang Sekolah</a>
            </nav>
            <a class="button small outline header-login" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ auth()->check() ? 'Ruang Kelola' : 'Masuk Pengelola' }} <span aria-hidden="true">↗</span></a>
        </div>
    </header>
    @if($school['demo'] ?? false)
        <div class="demo-strip">Ruang pratinjau · Identitas dan cerita di sini adalah contoh.</div>
    @endif

    <main id="main">
        @if(session('success'))
            <div class="alert success" role="status">✓ {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert error" role="alert"><strong>Periksa kembali isian berikut:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        @yield('content')
    </main>

    <footer>
        <div class="container footer-inner">
            <div><a class="brand" href="{{ route('home') }}"><span class="brand-icon" aria-hidden="true">✳</span><span class="brand-name">{{ $appearance['app_name'] }}</span></a><p>{{ $appearance['app_description'] }}</p></div>
            <div><strong>{{ $school['name'] }}</strong><p>{{ $appearance['footer_tagline'] }}</p><small>© {{ date('Y') }} · Dibuat untuk merayakan proses belajar.</small></div>
        </div>
    </footer>
@endif
</body>
</html>
