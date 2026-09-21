@extends('layouts.app')
@section('title', 'Kelas & tahun ajaran')
@section('content')
<div class="page-heading"><div><span class="eyebrow">FONDASI TEBAK AKSI</span><h1>Kelas & tahun ajaran</h1><p>Siapkan periode belajar dan tentukan guru yang mendampingi setiap rombel.</p></div><a class="button" href="{{ route('classrooms.create') }}">Tambah rombel</a></div>
<section class="panel">
    <h2>Periode belajar</h2>
    <p>Hanya satu tahun ajaran dan semester yang berjalan. Mengganti periode tidak memindahkan murid secara otomatis.</p>
    @forelse($years as $year)
    <form method="post" action="{{ route('years.activate', $year) }}" class="period-row" data-submit-label="Menyimpan periode…">
        @csrf @method('put')
        <div><strong>{{ $year->name }}</strong><small>{{ $year->starts_on->format('d/m/Y') }} – {{ $year->ends_on->format('d/m/Y') }}</small>@if($year->is_current)<span class="badge kegiatan">Berjalan · {{ ucfirst($year->semester) }}</span>@endif</div>
        <label>Semester {{ $year->name }}<select name="semester"><option value="ganjil" @selected($year->semester === 'ganjil')>Ganjil</option><option value="genap" @selected($year->semester === 'genap')>Genap</option></select></label>
        <button class="button outline">{{ $year->is_current ? 'Simpan semester' : 'Jadikan periode berjalan' }}</button>
    </form>
    @empty
    <p class="empty-note">Belum ada tahun ajaran. Tambahkan periode pertama di bawah ini.</p>
    @endforelse
    <details class="foundation-details" @if($years->isEmpty() || $errors->has('name') || $errors->has('starts_on') || $errors->has('ends_on')) open @endif>
        <summary>Tambah tahun ajaran</summary>
        <form method="post" action="{{ route('years.store') }}" data-submit-label="Menyimpan tahun ajaran…">
            @csrf
            <div class="two-col"><label>Tahun ajaran<input name="name" placeholder="2026/2027" required maxlength="9" value="{{ old('name') }}"></label><label>Tanggal mulai<input type="date" name="starts_on" required value="{{ old('starts_on') }}"></label><label>Tanggal selesai<input type="date" name="ends_on" required value="{{ old('ends_on') }}"></label></div>
            <button class="button outline">Simpan tahun ajaran</button>
        </form>
    </details>
</section>
<section class="panel">
    <h2>Rombongan belajar</h2>
    <p>Wali kelas dan guru pendamping dapat mengelola murid di rombel yang ditugaskan.</p>
    @if($classrooms->isEmpty())
        <p class="empty-note">Belum ada rombel. Setelah tahun ajaran dibuat, tambahkan kelas seperti I A atau V B.</p>
    @else
    <div class="data-scroll" role="region" aria-label="Daftar rombel" tabindex="0"><table class="school-table"><thead><tr><th scope="col">Rombel</th><th scope="col">Tahun ajaran</th><th scope="col">Penugasan guru</th><th scope="col">Murid terdaftar</th><th scope="col">Pengaturan</th></tr></thead><tbody>
    @foreach($classrooms as $classroom)
        <tr><th scope="row">{{ $classroom->name }}<small>Tingkat {{ $classroom->grade }}</small></th><td>{{ $classroom->academicYear->name }}</td><td>{{ $classroom->homeroomTeacher?->name ?? 'Belum ada wali kelas' }}<small>{{ $classroom->teachers->pluck('name')->join(', ') ?: 'Belum ada guru pendamping' }}</small></td><td><a href="{{ route('students.index', ['classroom' => $classroom->id]) }}">{{ $classroom->students_count }} murid</a></td><td><a class="table-action" href="{{ route('classrooms.edit', $classroom) }}">Atur {{ $classroom->name }}</a></td></tr>
    @endforeach
    </tbody></table></div>
    @endif
</section>
@endsection
