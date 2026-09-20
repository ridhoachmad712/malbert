<?php

return [
    'required' => ':attribute wajib diisi.',
    'required_if' => ':attribute wajib diisi.',
    'accepted' => ':attribute harus disetujui.',
    'accepted_if' => ':attribute harus disetujui.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Kata sandi saat ini tidak sesuai.',
    'min' => ['string' => ':attribute minimal :min karakter.'],
    'max' => ['string' => ':attribute maksimal :max karakter.', 'file' => ':attribute maksimal :max KB.', 'array' => ':attribute maksimal :max item.'],
    'image' => ':attribute harus berupa gambar.',
    'mimes' => 'Format :attribute harus :values.',
    'dimensions' => 'Dimensi :attribute terlalu besar.',
    'uploaded' => ':attribute gagal diunggah. Periksa batas ukuran file.',
    'date' => ':attribute harus berupa tanggal yang valid.',
    'regex' => 'Format :attribute tidak sesuai.',
    'unique' => ':attribute sudah digunakan.',
    'in' => 'Pilihan :attribute tidak valid.',
    'attributes' => ['title' => 'Judul', 'summary' => 'Ringkasan', 'body' => 'Cerita lengkap', 'cover' => 'Sampul', 'school_year' => 'Tahun ajaran', 'permission_confirmed' => 'Pemeriksaan izin publikasi', 'media_checked' => 'Pemeriksaan media', 'note' => 'Catatan perbaikan', 'password' => 'Kata sandi', 'email' => 'Email', 'name' => 'Nama', 'event_date' => 'Tanggal kegiatan'],
];
