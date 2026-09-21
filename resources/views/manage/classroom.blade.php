@extends('layouts.app')
@section('title', $classroom->exists ? 'Atur rombel' : 'Tambah rombel')
@section('content')
<a class="back-link" href="{{ route('foundation.index') }}">← Kelas & tahun ajaran</a>
<div class="page-heading"><div><span class="eyebrow">ROMBONGAN BELAJAR</span><h1>{{ $classroom->exists ? 'Atur '.$classroom->name : 'Tambah rombel' }}</h1><p>Nama rombel membedakan kelas paralel, misalnya I A dan I B.</p></div></div>
@if($years->isEmpty())
<section class="panel"><p>Tambahkan tahun ajaran sebelum membuat rombel.</p><a class="button" href="{{ route('foundation.index') }}">Atur tahun ajaran</a></section>
@else
<form class="panel" method="post" action="{{ $classroom->exists ? route('classrooms.update', $classroom) : route('classrooms.store') }}" data-dirty-form data-submit-label="Menyimpan rombel…">
    @csrf
    @if($classroom->exists) @method('put') <p>Tahun ajaran <strong>{{ $classroom->academicYear->name }}</strong>. Untuk tahun berikutnya, buat rombel baru agar riwayat tetap tersimpan.</p> @else
    <label>Tahun ajaran<select name="academic_year_id" required><option value="">Pilih tahun ajaran</option>@foreach($years as $year)<option value="{{ $year->id }}" @selected(old('academic_year_id', $years->firstWhere('is_current', true)?->id) == $year->id)>{{ $year->name }}</option>@endforeach</select></label>
    @endif
    <div class="two-col"><label>Nama rombel<input name="name" required maxlength="40" placeholder="I A" value="{{ old('name', $classroom->name) }}"></label><label>Tingkat<select name="grade" required>@for($grade = 1; $grade <= 6; $grade++)<option value="{{ $grade }}" @selected(old('grade', $classroom->grade) == $grade)>Kelas {{ $grade }}</option>@endfor</select></label></div>
    <h2>Guru yang ditugaskan</h2>
    <label>Wali kelas<select name="homeroom_teacher_id"><option value="">Belum ditentukan</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}" @selected(old('homeroom_teacher_id', $classroom->homeroom_teacher_id) == $teacher->id)>{{ $teacher->name }}</option>@endforeach</select></label>
    <fieldset class="teacher-options"><legend>Guru pendamping</legend><p>Pilih guru lain yang perlu mengelola data murid di kelas ini. Wali kelas sudah memperoleh akses.</p>
    @forelse($teachers as $teacher)<label class="check"><input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}" @checked(in_array($teacher->id, old('teacher_ids', session()->hasOldInput() ? [] : $classroom->teachers->modelKeys())))> {{ $teacher->name }}</label>@empty<p>Belum ada akun guru aktif. Buat akun melalui menu Pengguna.</p>@endforelse
    </fieldset>
    <button class="button">Simpan rombel & penugasan</button>
</form>
@endif
@endsection
