@extends('layouts.app')
@section('title', 'Ringkasan pekerjaan')
@section('content')
<div class="page-heading">
    <div>
        <span class="eyebrow">{{ $isAdmin ? 'RINGKASAN SEKOLAH' : 'RUANG GURU' }}</span>
        <h1>Halo, {{ auth()->user()->name }}</h1>
        <p>{{ $isAdmin ? 'Pantau kegiatan belajar dan periksa karya yang siap diterbitkan.' : 'Mulai dari kelas dan tugas, lalu tinjau hasil belajar murid.' }}</p>
    </div>
    <div class="heading-actions">
        @if($isAdmin)
            <a class="button outline" href="{{ route('foundation.index') }}">Atur kelas</a>
            <a class="button" href="{{ route('monitoring.index') }}">Lihat monitoring</a>
        @else
            <a class="button outline" href="{{ route('literacy.index') }}">Lihat literasi</a>
            @if($stats['classrooms'] > 0)<a class="button" href="{{ route('literacy.assignments.create') }}">Buat tugas</a>@endif
        @endif
    </div>
</div>

@if(! $isAdmin && $stats['classrooms'] === 0)
    <div class="alert info" role="status">Anda belum ditugaskan ke rombel. Minta admin sekolah menambahkan penugasan agar Anda dapat membuat tugas dan mencatat hasil murid.</div>
@endif

<div class="stats-grid" aria-label="Ringkasan pekerjaan">
    <div class="stat"><span>Rombel dapat diakses</span><strong>{{ $stats['classrooms'] }}</strong></div>
    <div class="stat"><span>Tugas aktif</span><strong>{{ $stats['activeAssignments'] }}</strong></div>
    <div class="stat"><span>Hasil menunggu tinjauan</span><strong>{{ $stats['pendingSubmissions'] }}</strong></div>
    <div class="stat"><span>Hasil disetujui belum dinilai</span><strong>{{ $stats['unassessedSubmissions'] }}</strong></div>
</div>

<section class="panel dashboard-panel" aria-labelledby="dashboard-review-title">
    <div class="section-heading">
        <div><h2 id="dashboard-review-title">Hasil murid untuk ditinjau</h2><p>Periksa jurnal, karya, dan refleksi sebelum memberi penilaian.</p></div>
        <a href="{{ route('literacy.index') }}">Semua tugas →</a>
    </div>
    <div class="literacy-list">
        @forelse($submissionsToReview as $submission)
            <a class="literacy-list-item" href="{{ route('literacy.submissions.edit', [$submission->assignment, $submission->student]) }}">
                <span><strong>{{ $submission->student->name }}</strong><small>{{ $submission->assignment->title }} · {{ $submission->assignment->classroom->name }}</small></span>
                <span class="badge karya">Periksa hasil</span>
            </a>
        @empty
            <p class="empty-note dashboard-empty">Belum ada hasil murid yang menunggu tinjauan.</p>
        @endforelse
    </div>
</section>

<section class="panel dashboard-panel" aria-labelledby="dashboard-assessment-title">
    <div class="section-heading">
        <div><h2 id="dashboard-assessment-title">Hasil disetujui untuk dinilai</h2><p>Gunakan rubrik literasi setelah hasil murid disetujui.</p></div>
        <a href="{{ route('evaluation.index') }}">Penilaian & poin →</a>
    </div>
    @if(! $assessmentConfigured)
        <div class="alert warning"><strong>Penilaian menunggu aturan sekolah.</strong><p>{{ $isAdmin ? 'Tetapkan aturan sebelum guru menilai hasil murid.' : 'Admin perlu menetapkan aturan sebelum hasil murid dapat dinilai.' }} @if($isAdmin)<a href="{{ route('evaluation.settings') }}">Atur penilaian</a>@endif</p></div>
    @endif
    <div class="literacy-list">
        @forelse($submissionsToAssess as $submission)
            <a class="literacy-list-item" href="{{ $assessmentConfigured ? route('evaluation.assessment', $submission) : route('evaluation.index') }}">
                <span><strong>{{ $submission->student->name }}</strong><small>{{ $submission->assignment->title }} · {{ $submission->assignment->classroom->name }}</small></span>
                <span class="badge karya">{{ $assessmentConfigured ? 'Nilai hasil' : 'Menunggu aturan' }}</span>
            </a>
        @empty
            <p class="empty-note dashboard-empty">Belum ada hasil yang siap dinilai.</p>
        @endforelse
    </div>
</section>

<section class="panel dashboard-panel" aria-labelledby="dashboard-assignment-title">
    <div class="section-heading">
        <div><h2 id="dashboard-assignment-title">Tugas literasi terbaru</h2><p>Buka tugas untuk mencatat hasil murid atau melanjutkan penilaian.</p></div>
        <a href="{{ route('literacy.index') }}">Kelola literasi →</a>
    </div>
    <div class="literacy-list">
        @forelse($recentAssignments as $assignment)
            <a class="literacy-list-item" href="{{ route('literacy.assignments.show', $assignment) }}">
                <span><strong>{{ $assignment->title }}</strong><small>{{ $assignment->classroom->name }} · {{ $assignment->assigned_on->translatedFormat('d M Y') }}</small></span>
                <span class="badge {{ $assignment->status === 'active' ? 'kegiatan' : 'karya' }}">{{ ['draft' => 'Draf', 'active' => 'Aktif', 'closed' => 'Ditutup'][$assignment->status] }}</span>
            </a>
        @empty
            <p class="empty-note dashboard-empty">@if($stats['classrooms'] > 0)Belum ada tugas pada rombel yang dapat Anda akses. <a href="{{ route('literacy.assignments.create') }}">Buat tugas literasi</a>.@else Tugas literasi akan muncul setelah penugasan rombel tersedia.@endif</p>
        @endforelse
    </div>
</section>

@if($isAdmin)
    <section class="panel dashboard-panel" aria-labelledby="dashboard-publication-review-title">
        <div class="section-heading">
            <div><h2 id="dashboard-publication-review-title">Publikasi menunggu persetujuan ({{ $stats['pendingPublications'] }})</h2><p>Periksa kelayakan konten dan izin sebelum karya tampil di situs publik.</p></div>
            <a href="{{ route('publications.index', ['status' => 'pending']) }}">Semua tinjauan →</a>
        </div>
        <div class="literacy-list">
            @forelse($publicationsToReview as $publication)
                <a class="literacy-list-item" href="{{ route('publications.edit', $publication) }}">
                    <span><strong>{{ $publication->content['title'] }}</strong><small>{{ $publication->author->name }} · {{ ucfirst($publication->content['type']) }}</small></span>
                    <span class="badge karya">Tinjau</span>
                </a>
            @empty
                <p class="empty-note dashboard-empty">Belum ada publikasi yang menunggu persetujuan.</p>
            @endforelse
        </div>
    </section>
@endif

<section class="panel dashboard-panel" aria-labelledby="dashboard-publication-title">
    <div class="section-heading">
        <div><h2 id="dashboard-publication-title">{{ $isAdmin ? 'Cerita sekolah terbaru' : 'Cerita yang Anda kelola' }}</h2><p>Kegiatan dan karya terpilih dikelola terpisah dari catatan belajar internal.</p></div>
        <a href="{{ route('publications.index') }}">Semua cerita →</a>
    </div>
    <div class="literacy-list">
        @forelse($recentPublications as $publication)
            <a class="literacy-list-item" href="{{ route('publications.edit', $publication) }}">
                <span><strong>{{ $publication->content['title'] }}</strong><small>{{ $publication->author->name }} · {{ $publication->updated_at->translatedFormat('d M Y') }}</small></span>
                <span class="badge {{ $publication->status === 'published' ? 'kegiatan' : 'karya' }}">{{ ['draft' => 'Draf', 'pending' => 'Menunggu', 'published' => 'Terbit', 'archived' => 'Arsip'][$publication->status] }}</span>
            </a>
        @empty
            <p class="empty-note dashboard-empty">Belum ada cerita sekolah yang Anda kelola.</p>
        @endforelse
    </div>
</section>

<section class="panel activity-panel"><h2>Aktivitas terakhir</h2>@forelse($logs as $log)<div class="activity-line"><span class="activity-mark" aria-hidden="true">↗</span><div><strong>{{ $log->action }}</strong><p>{{ $log->subject }}</p></div><small>{{ $log->created_at->diffForHumans() }}</small></div>@empty<p class="muted">Aktivitas pengelolaan akan tercatat di sini.</p>@endforelse</section>
@endsection
