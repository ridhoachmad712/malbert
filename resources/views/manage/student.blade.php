@extends('layouts.app')
@section('title', $student->exists ? 'Data murid' : 'Tambah murid')
@section('content')
<a class="back-link" href="{{ route('students.index') }}">← Daftar murid</a>
<div class="page-heading"><div><span class="eyebrow">TEBAK AKSI · DATA INTERNAL</span><h1>{{ $student->exists ? $student->name : 'Tambah murid' }}</h1><p>Identitas murid disimpan untuk kegiatan belajar dan tidak ditampilkan di galeri publik.</p></div></div>
@if($classrooms->isEmpty())
<section class="panel"><p>Belum ada rombel yang dapat Anda kelola. Hubungi admin untuk mengatur kelas dan penugasan.</p></section>
@else
<form class="panel" method="post" action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}" data-dirty-form data-submit-label="Menyimpan murid…">
    @csrf
    @if($student->exists) @method('put') @endif
    <h2>Identitas murid</h2>
    <label>Nama lengkap<input name="name" required maxlength="120" autocomplete="off" value="{{ old('name', $student->name) }}"></label>
    <div class="two-col"><label>NIS<input name="nis" inputmode="numeric" pattern="[0-9]{1,30}" maxlength="30" value="{{ old('nis', $student->nis) }}"></label><label>NISN<input name="nisn" inputmode="numeric" pattern="[0-9]{10}" maxlength="10" value="{{ old('nisn', $student->nisn) }}"></label></div>
    <p class="field-hint">Isi setidaknya NIS atau NISN. Gunakan angka; NISN tepat 10 digit. Nol di depan akan disimpan.</p>
    @if(!$student->exists)
    <div class="two-col"><label>Rombel<select name="classroom_id" required><option value="">Pilih rombel</option>@foreach($classrooms as $classroom)<option value="{{ $classroom->id }}" @selected(old('classroom_id') == $classroom->id)>{{ $classroom->name }} · {{ $classroom->academicYear->name }}</option>@endforeach</select></label><label>Tanggal masuk kelas<input type="date" name="starts_on" required max="{{ now()->toDateString() }}" value="{{ old('starts_on', now()->toDateString()) }}"></label></div>
    @else
    <p>Kelas saat ini: <strong>{{ $student->classroom->name }} · {{ $student->classroom->academicYear->name }}</strong></p>
    @endif
    @if(auth()->user()->role === 'admin')
    <div class="two-col"><label>Status murid<select name="active"><option value="1" @selected(old('active', $student->exists ? (int)$student->active : 1) == 1)>Aktif</option><option value="0" @selected(old('active', $student->exists ? (int)$student->active : 1) == 0)>Nonaktif</option></select></label><label>Verifikasi identitas<select name="verified"><option value="0" @selected(old('verified', (int)(bool)$student->verified_at) == 0)>Belum diverifikasi</option><option value="1" @selected(old('verified', (int)(bool)$student->verified_at) == 1)>Sudah diperiksa admin</option></select></label></div>
    @else
    <p>{{ $student->verified_at ? 'Identitas telah diverifikasi.' : 'Identitas menunggu verifikasi admin.' }} Perubahan nama atau nomor murid akan memerlukan pemeriksaan ulang.</p>
    @endif
    @if($student->verified_at)<p class="field-hint">Diperiksa {{ $student->verified_at->format('d/m/Y H:i') }} oleh {{ $student->verifier?->name }}.</p>@endif
    <button class="button">Simpan data murid</button>
</form>
@if($student->exists)
<section class="panel"><h2>Perjalanan kelas</h2><p>Riwayat tetap tersimpan saat murid pindah atau naik kelas.@if(auth()->user()->role !== 'admin') Anda hanya melihat riwayat pada kelas yang ditugaskan kepada Anda.@endif</p>
    <ol class="class-history">@foreach($history as $entry)<li><strong>{{ $entry->classroom->name }} · {{ $entry->classroom->academicYear->name }}</strong><span>Masuk {{ $entry->starts_on->format('d/m/Y') }} · {{ $entry->ends_on ? 'Pindah pada '.$entry->ends_on->format('d/m/Y') : 'Kelas saat ini' }}</span></li>@endforeach</ol>
</section>
@if(auth()->user()->role === 'admin')
<details class="panel foundation-details" @if($errors->has('classroom_id') || $errors->has('starts_on')) open @endif><summary>Pindah atau naik kelas</summary><p>Pilih rombel tujuan dan tanggal perpindahan. Riwayat lama ditutup pada tanggal ini; data murid tetap sama. Untuk murid lulus atau keluar, ubah status menjadi Nonaktif.</p>
    <form method="post" action="{{ route('students.transfer', $student) }}" data-submit-label="Menyimpan perpindahan…">
        @csrf
        <div class="two-col"><label>Rombel tujuan<select name="classroom_id" required><option value="">Pilih kelas berbeda</option>@foreach($classrooms->where('id', '!=', $student->classroom_id) as $classroom)<option value="{{ $classroom->id }}" @selected(old('classroom_id') == $classroom->id)>{{ $classroom->name }} · {{ $classroom->academicYear->name }}</option>@endforeach</select></label><label>Tanggal pindah<input type="date" name="starts_on" required max="{{ now()->toDateString() }}" value="{{ old('starts_on', now()->toDateString()) }}"></label></div><button class="button outline">Simpan perpindahan kelas</button>
    </form>
</details>
@endif
@endif
@endif
@endsection
