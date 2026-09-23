@extends('layouts.app')
@section('title','Profil Sekolah')
@section('content')
<div class="page-heading">
    <div><span class="eyebrow">IDENTITAS SEKOLAH</span><h1>Kenalkan sekolah kita</h1><p>Informasi ini akan tampil di halaman publik.</p></div>
</div>
<form method="post" enctype="multipart/form-data" action="{{ route('settings.update') }}" class="panel form-panel">
    @csrf @method('put')
    <label>Logo sekolah<input type="file" name="logo" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG, atau WebP, maksimal 10 MB. Kosongkan untuk mempertahankan logo saat ini.</small></label>
    <div class="two-col">
        <label>Nama resmi sekolah<input name="name" value="{{ old('name', $school['name'] ?? '') }}" required autocomplete="organization"></label>
        <label>NPSN<input name="npsn" value="{{ old('npsn', $school['npsn'] ?? '') }}" inputmode="numeric" pattern="[0-9]{8}" maxlength="8" placeholder="8 digit NPSN"></label>
        <label>Email resmi<input type="email" name="email" value="{{ old('email', $school['email'] ?? '') }}" autocomplete="email"></label>
        <label>Nomor telepon resmi<input type="tel" name="phone" value="{{ old('phone', $school['phone'] ?? '') }}" autocomplete="tel"></label>
    </div>
    <label>Alamat sekolah<input name="address" value="{{ old('address', $school['address'] ?? '') }}" autocomplete="street-address"></label>
    <label>Kalimat pengantar<input name="tagline" value="{{ old('tagline', $school['tagline'] ?? '') }}" required><small>Satu kalimat singkat yang memperkenalkan sekolah pada halaman publik.</small></label>
    @foreach(['description' => 'Tentang sekolah', 'vision' => 'Visi', 'mission' => 'Misi'] as $key => $label)
        <label>{{ $label }}<textarea name="{{ $key }}" rows="4">{{ old($key, $school[$key] ?? '') }}</textarea></label>
    @endforeach
    <label class="check-label"><input type="checkbox" name="demo" value="1" @checked(old('demo', $school['demo'] ?? false))><span>Tampilkan penanda bahwa aplikasi masih berisi data contoh.</span></label>
    <button class="button">Simpan profil sekolah</button>
</form>
@endsection
