# Serah terima LIT-001

- Tanggal/waktu/zona: 2026-09-21, Asia/Makassar.
- Agen/sesi: Codex lokal; tanpa pekerja paralel.
- Status: DONE. Kriteria tahap 2 telah diterapkan dan diuji; ini bukan status siap rilis seluruh aplikasi.
- Tujuan dan kriteria penerimaan: Pojok Baca, tautan/QR, tugas per kelas dan tenggat, jurnal, karya, refleksi, tinjauan serta revisi; data terikat murid/kelas/periode; karya tidak otomatis publik.
- Cabang, revisi dasar, revisi kode yang diuji: `main`, dasar `476d080`; hasil final adalah commit yang memuat catatan ini.
- Commit hasil atau cara menemukannya: `git log -1 -- instruction/serah-terima/HANDOFF_LIT_001.md`.
- Pemilik selanjutnya: tidak ada; periksa TASKS.md sebelum mengambil NIL-001.

## Hasil dan berkas berubah

Empat model dan migrasi baru menyimpan bahan bacaan, tugas literasi, hasil murid, dan riwayat tinjauan. Tugas menyimpan snapshot tahun ajaran dan semester kelas. Satu hasil per murid per tugas memuat jurnal membaca, karya opsional, tiga refleksi, status, nomor revisi, umpan balik, dan peninjau. Setiap pengajuan, permintaan perbaikan, serta persetujuan menyimpan snapshot agar perubahan dapat ditelusuri.

Guru hanya mengakses tugas dan hasil pada rombel yang ditugaskan. Bacaan guru tetap internal; admin dapat menerbitkannya ke Pojok Baca. Kode QR berupa gambar opsional yang hanya tersedia jika bacaan publik. Karya tulisan, tautan HTTPS, foto, atau PDF tidak memiliki rute publik; foto/PDF disimpan pada disk lokal privat dan diunduh melalui pemeriksaan akses kelas dengan respons `private, no-store`.

Halaman baru mencakup Pojok Baca publik serta daftar literasi, editor bacaan/tugas, roster hasil murid, formulir jurnal-karya-refleksi, peninjauan, dan riwayat revisi. Navigasi publik dan pengelola diperbarui. Seeder lokal memberi contoh bacaan/tugas setelah fondasi demo. Backup versi 3 mencakup empat tabel literasi, QR, dan berkas karya; restore versi 1 dan 2 tetap diterima.

## Bukti pemeriksaan

| Perintah/interaksi | Waktu | Hasil | Batas pemeriksaan |
| --- | --- | --- | --- |
| `php artisan test --compact tests/Feature/LiteracyWorkflowTest.php` | 2026-09-21 | 9 tes / 67 assertions lulus | SQLite in-memory; mencakup alur penuh, akses, media privat, render, escaping, dan backup |
| `vendor/bin/phpunit --testdox` | 2026-09-21 | 57 tes / 398 assertions lulus | Regresi seluruh aplikasi; Drive tetap memakai respons tiruan |
| `vendor/bin/pint --dirty --format agent` | 2026-09-21 | Lulus | Seluruh PHP yang berubah |
| `node --check public/js/app.js` | 2026-09-21 | Lulus | Pemeriksaan sintaks; tidak ada JavaScript literasi baru |
| `php artisan route:list --except-vendor` | 2026-09-21 | 53 rute berhasil dimuat | Membuktikan controller/rute dapat di-bootstrap |
| `git diff --check` | 2026-09-21 | Lulus | Git hanya memberi pemberitahuan normalisasi CRLF CSS |
| Browser lokal `/pojok-baca` | 2026-09-21 | Judul, navigasi, keadaan kosong, footer, dan fokus informasi tampil tanpa overflow terlihat pada 668 × 840 | Database lokal tidak diberi data QA; kartu dengan data dibuktikan lewat tes render |

## Kondisi belum selesai

Tidak ada kriteria wajib LIT-001 yang tersisa. Uji bersama sekolah, audit aksesibilitas menyeluruh, data bacaan nyata, deployment, dan penjadwalan backup berada di luar tugas ini. Pojok Baca memakai unggahan gambar QR; aplikasi tidak menambah dependensi pembangkit QR. Retensi lampiran privat masih merupakan keputusan lintas tahap yang terbuka.

## Langkah berikutnya

Sebelum NIL-001, tetapkan aturan nilai kemampuan, poin aktivitas, koreksi, batas frekuensi, dan definisi capaian yang masih terbuka di rancangan. Setelah keputusan tersedia, buat kontrak data penilaian yang merujuk murid, kelas, tahun ajaran, semester, dan aktivitas sumber tanpa mengubah snapshot literasi.

## Keputusan dan integrasi

Implementasi mengikuti D-002, D-003, D-007, dan D-010. Perubahan berada langsung pada `main` dan dicatat dalam commit checkpoint lokal. Remote masih belum dikonfigurasi, sehingga tidak ada push. Tidak ada password, token, atau data murid nyata dalam checkpoint.
