@extends('layouts.app')
@section('title', 'Data murid')
@section('content')
<div class="page-heading"><div><span class="eyebrow">TEBAK AKSI · DATA INTERNAL</span><h1>Kenali murid di kelasmu</h1><p>{{ auth()->user()->role === 'admin' ? 'Kelola identitas, verifikasi, dan perjalanan kelas murid.' : 'Daftar ini hanya memuat murid pada rombel yang ditugaskan kepada Anda.' }}</p></div>@if($classrooms->isNotEmpty())<a class="button" href="{{ route('students.create') }}">Tambah murid</a>@endif</div>
@if($classrooms->isEmpty())
<section class="panel"><h2>{{ auth()->user()->role === 'admin' ? 'Siapkan rombel pertama' : 'Belum ada penugasan kelas' }}</h2><p>{{ auth()->user()->role === 'admin' ? 'Tambahkan tahun ajaran dan rombel sebelum mendaftarkan murid.' : 'Minta admin menetapkan Anda sebagai wali kelas atau guru pendamping. Setelah itu, data kelas akan tampil di sini.' }}</p>@if(auth()->user()->role === 'admin')<a class="button" href="{{ route('foundation.index') }}">Atur kelas & tahun ajaran</a>@endif</section>
@else
<section class="panel">
    <form method="get" action="{{ route('students.index') }}" class="student-filters">
        <label>Cari murid<input type="search" name="q" maxlength="120" placeholder="Nama, NIS, atau NISN" value="{{ request('q') }}"></label>
        <label>Tahun kelas saat ini<select name="year"><option value="">Semua tahun</option>@foreach($years as $year)<option value="{{ $year->id }}" @selected(request('year') == $year->id)>{{ $year->name }}</option>@endforeach</select></label>
        <label>Rombel saat ini<select name="classroom"><option value="">Semua rombel</option>@foreach($classrooms as $classroom)<option value="{{ $classroom->id }}" @selected(request('classroom') == $classroom->id)>{{ $classroom->name }} · {{ $classroom->academicYear->name }}</option>@endforeach</select></label>
        <label>Status<select name="status"><option value="">Semua status</option>@foreach(['active'=>'Aktif', 'inactive'=>'Nonaktif', 'pending'=>'Belum diverifikasi', 'verified'=>'Terverifikasi'] as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></label>
        <div class="filter-actions"><button class="button outline">Cari murid</button><a class="table-action" href="{{ route('students.index') }}">Hapus filter</a></div>
    </form>
    <h2 class="list-heading">{{ $students->total() }} murid ditemukan</h2>
    @if($students->isEmpty())<p class="empty-note">Belum ada murid yang sesuai. Periksa filter, tambahkan murid satu per satu, atau impor daftar kelas di bawah.</p>@else
    <div class="data-scroll" role="region" aria-label="Daftar murid" tabindex="0"><table class="school-table"><thead><tr><th scope="col">Murid</th><th scope="col">Rombel saat ini</th><th scope="col">Status</th><th scope="col">Data & riwayat</th></tr></thead><tbody>
        @foreach($students as $student)<tr><th scope="row">{{ $student->name }}<small>NIS {{ $student->nis ?? 'belum diisi' }} · NISN {{ $student->nisn ?? 'belum diisi' }}</small></th><td>{{ $student->classroom->name }}<small>{{ $student->classroom->academicYear->name }}</small></td><td>{{ $student->active ? 'Aktif' : 'Nonaktif' }}<small>{{ $student->verified_at ? 'Terverifikasi' : 'Belum diverifikasi' }}</small></td><td><a class="table-action" href="{{ route('students.edit', $student) }}">Lihat data<span class="sr-only"> {{ $student->name }}</span></a></td></tr>@endforeach
    </tbody></table></div>
    {{ $students->links() }}
    @endif
</section>
<details class="panel foundation-details" @if($errors->any()) open @endif>
    <summary>Impor daftar murid dari CSV</summary>
    <p>Untuk murid baru, maksimal 500 baris dan 2 MB. Kolom wajib: <strong>nama, nis, nisn</strong>. Isi setidaknya NIS atau NISN; NISN harus 10 digit. Simpan sebagai CSV UTF-8 dari Excel dengan kolom nomor berformat teks agar nol di depan tetap ada.</p>
    <p>Nomor yang sudah terdaftar akan ditolak, bukan ditimpa. Jika ada kesalahan, tidak ada baris yang disimpan. Perbaiki baris yang disebutkan lalu pilih ulang berkas. Pemisah koma maupun titik koma didukung.</p>
    <a class="table-action" href="{{ route('students.template') }}">Unduh template CSV</a>
    <form method="post" action="{{ route('students.import') }}" enctype="multipart/form-data" data-submit-label="Memeriksa berkas…">
        @csrf
        <div class="two-col"><label>Rombel tujuan<select name="classroom_id" required><option value="">Pilih rombel</option>@foreach($classrooms as $classroom)<option value="{{ $classroom->id }}" @selected(old('classroom_id') == $classroom->id)>{{ $classroom->name }} · {{ $classroom->academicYear->name }}</option>@endforeach</select></label><label>Tanggal masuk kelas<input type="date" name="starts_on" required max="{{ now()->toDateString() }}" value="{{ old('starts_on', now()->toDateString()) }}"></label></div>
        <label>Berkas daftar murid<input type="file" name="file" required accept=".csv,text/csv"></label><button class="button">Periksa & impor murid</button>
    </form>
</details>
@endif
@endsection
