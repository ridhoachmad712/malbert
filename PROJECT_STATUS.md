# Status proyek

Diperbarui: 2026-09-20 oleh Codex, tugas DOC-002.

Dokumentasi disatukan di [instruction](instruction/README.md). Catatan penataan terbaru: [DOC-002](instruction/serah-terima/HANDOFF_DOC_002.md). Fitur aplikasi tidak berubah.

Pembaruan dokumentasi DOC-001: catatan untuk pihak sekolah disimpan pada [Kebutuhan data dan informasi sekolah](instruction/sekolah/KEBUTUHAN_DATA_DAN_INFORMASI_SEKOLAH.md). Tidak ada perubahan fitur aplikasi; data sekolah masih menunggu pengumpulan.

## Kondisi produk

- Aplikasi Laravel Ruang Cerita berjalan lokal sebagai dasar TEBAK AKSI.
- Tersedia: akun admin/guru, publikasi kegiatan/karya/prestasi, draf/tinjauan/revisi/arsip, pencarian, profil/logo sekolah, foto terkompresi, tautan Drive, dan pencadangan.
- Impor folder Drive dan slider sudah memiliki kode dan tes, tetapi koneksi nyata belum selesai. Jangan mengklaim API sudah aktif. Proses Google Cloud terakhir tertahan persyaratan verifikasi dua langkah; status akun perlu diperiksa kembali bila melanjutkan.
- Data murid, rombel, jurnal, tugas, penilaian/poin, monitoring, laporan, pembinaan, dan pengaduan belum diimplementasikan.
- Data yang terlihat masih contoh. Belum deployment online. Penjadwalan cadangan pada server belum diaktifkan.
- Anti-slop diadopsi sebagai panduan lokal; audit menyeluruh UI lama belum dilakukan.

## Pekerjaan berikutnya

1. FND-001: desain dan implementasi fondasi tahun ajaran, semester, rombel, data murid, riwayat keanggotaan dan penugasan guru. Pecah menjadi subtugas dengan kontrak data sebelum membagi pekerjaan paralel.
2. DRV-001 dapat dilanjutkan terpisah bila akses Google Cloud tersedia.
3. OPS-001: menghubungkan remote privat agar akun/perangkat/cloud lain dapat mengambil revisi terbaru. Belum ada tujuan remote yang dipilih pengguna.

## Bukti dan batas verifikasi

Pengujian aplikasi terakhir sebelum CTX-001: 22 pengujian, 157 assertions, lulus; tes Drive memakai respons tiruan. Pada CTX-001 hanya dokumentasi dan pengaturan Git yang berubah. Catatan pemeriksaan terbaru berada pada [HANDOFF_CTX_001.md](instruction/serah-terima/HANDOFF_CTX_001.md).

## Koordinasi

Tidak ada agen pekerja yang sedang dialokasikan setelah serah terima CTX-001. Sesi pertama yang melanjutkan bertindak sebagai koordinator sampai ada penunjukan lain. Ini bukan izin untuk mengabaikan klaim tugas yang lebih baru.

Git lokal disiapkan pada CTX-001. Remote belum dihubungkan dan tidak ada push. Revisi awal dapat ditemukan dengan `git log --oneline`; kondisi sebenarnya selalu diperiksa dengan Git, bukan disimpulkan dari teks ini.

## Lokasi penting

| Area | Lokasi |
| --- | --- |
| Alur publikasi | `app/Http/Controllers/PublicationController.php` |
| Album Drive | `app/Services/DriveAlbumService.php` |
| Validasi konten | `app/Http/Requests/SavePublicationRequest.php` |
| Tampilan | `resources/views`, `public/css/app.css`, `public/js/app.js` |
| Tes | `tests/Feature/SchoolApplicationTest.php`, `tests/Feature/DriveAlbumTest.php` |
| Data awal | `database/migrations`, `database/seeders` |
| Konfigurasi contoh | `.env.example` |
| Rancangan dan sumber | `instruction/sumber` |

Dokumen ini adalah ringkasan keadaan terkini. Aturan bisnis tetap berada pada rancangan; keputusan dan perubahan alasan berada pada DECISIONS.md.
