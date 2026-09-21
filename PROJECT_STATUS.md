# Status proyek

Diperbarui: 2026-09-21 oleh Codex, finalisasi MON-001.

Fondasi, literasi, penilaian/apresiasi, serta monitoring/laporan sudah diimplementasikan, diuji, dan diintegrasikan. Bukti terbaru: [HANDOFF_MON_001.md](instruction/serah-terima/HANDOFF_MON_001.md); tahap sebelumnya: [HANDOFF_NIL_001.md](instruction/serah-terima/HANDOFF_NIL_001.md).

Dokumentasi disatukan di [instruction](instruction/README.md). Catatan penataan terbaru: [DOC-002](instruction/serah-terima/HANDOFF_DOC_002.md). Fitur aplikasi tidak berubah.

Pembaruan dokumentasi DOC-001: catatan untuk pihak sekolah disimpan pada [Kebutuhan data dan informasi sekolah](instruction/sekolah/KEBUTUHAN_DATA_DAN_INFORMASI_SEKOLAH.md). Tidak ada perubahan fitur aplikasi; data sekolah masih menunggu pengumpulan.

## Kondisi produk

- Aplikasi Laravel Ruang Cerita berjalan lokal sebagai dasar TEBAK AKSI.
- Tersedia: akun admin/guru, publikasi kegiatan/karya/prestasi, draf/tinjauan/revisi/arsip, pencarian, profil/logo sekolah, foto terkompresi, tautan Drive, dan pencadangan.
- Impor folder Drive dan slider sudah memiliki kode dan tes, tetapi koneksi nyata belum selesai. Jangan mengklaim API sudah aktif. Proses Google Cloud terakhir tertahan persyaratan verifikasi dua langkah; status akun perlu diperiksa kembali bila melanjutkan.
- Tahun ajaran, pilihan semester berjalan, rombel, penugasan guru, data murid, impor CSV, verifikasi/status aktif, dan riwayat perpindahan sudah tersedia pada working tree. Akses guru mengikuti rombel saat ini. Filter daftar murid memang memakai kelas saat ini, bukan laporan historis.
- Pojok Baca publik, bahan bacaan internal, tugas per rombel, jurnal membaca, karya tulisan/tautan/foto/PDF, refleksi, tinjauan, revisi, persetujuan, dan riwayat snapshot sudah tersedia. Hasil dan berkas karya tetap privat serta tidak otomatis menjadi publikasi.
- Penilaian dari rubrik enam indikator, nilai dan kategori otomatis, umpan balik, transaksi poin aktivitas, tingkatan apresiasi per periode, pencegahan poin ganda, serta koreksi bernalar dan terlacak sudah tersedia. Admin wajib menyimpan konfigurasi aturan sekolah sebelum modul digunakan.
- Monitoring per kelas/periode, perkembangan bulanan murid, konfigurasi capaian 80%, pembedaan data belum dinilai, laporan bulanan cetak, dan ekspor CSV sudah tersedia. Angka menggunakan tanggal kegiatan sumber dan akses guru mengikuti rombel.
- Pembinaan dan pengaduan belum diimplementasikan. Publikasi yang ada belum terhubung langsung dengan bank karya internal.
- Data yang terlihat masih contoh. Belum deployment online. Penjadwalan cadangan pada server belum diaktifkan.
- Anti-slop diadopsi sebagai panduan lokal; audit menyeluruh UI lama belum dilakukan.

## Pekerjaan berikutnya

1. PUB-001: hubungkan karya internal yang disetujui dengan seleksi dan publikasi tanpa membuka catatan internal. Tugas ini siap dan tidak perlu menunggu modul pembinaan.
2. DRV-001 dapat dilanjutkan terpisah bila akses Google Cloud tersedia.
3. OPS-001: menghubungkan remote privat agar akun/perangkat/cloud lain dapat mengambil revisi terbaru. Belum ada tujuan remote yang dipilih pengguna.

## Bukti dan batas verifikasi

Pengujian terbaru 2026-09-21: 73 tes / 497 assertions lulus, termasuk 7 tes monitoring / 37 assertions dan tes regresi seluruh aplikasi. Pint, pemeriksaan sintaks JavaScript, daftar 65 rute, dan git diff --check lulus. Tes monitoring membuktikan gerbang konfigurasi, populasi/penyebut, data belum dinilai, rata-rata/nilai terbaru, periode sumber, koreksi poin, batas akses, keamanan CSV, dan backup versi 5. QA browser memakai SQLite terpisah dengan data contoh: dasbor, perkembangan murid, laporan, filter, keadaan kosong, CSV, dan cetak diperiksa pada 668 × 840 serta 1280 × 900 tanpa overflow halaman atau error konsol. Tes Drive tetap memakai respons tiruan. Audit UI menyeluruh dan uji bersama sekolah belum dilakukan.

Saat pemeriksaan migrasi, perintah CLI `migrate:fresh --env=testing` ternyata masih menunjuk database MySQL lokal dan meresetnya sebelum migrasi gagal karena nama indeks terlalu panjang. Nama indeks sudah diperpendek, tabel parsial kosong dihapus, migrasi dijalankan ulang, lalu database dipulihkan dari cadangan terbaru yang tersedia `school-20260919-164003-UtkKg9.zip`. Isi yang pulih sesuai arsip: 2 pengguna, 7 publikasi, 1 pengaturan sekolah, tanpa tahun ajaran atau murid. QA selanjutnya memakai SQLite terpisah dengan target database eksplisit. Jangan memakai `migrate:fresh` CLI tanpa memeriksa target database aktual.

## Koordinasi

Tidak ada pekerja aktif setelah finalisasi MON-001. Periksa TASKS.md dan status aktual sebelum mengambil tugas berikutnya.

Git lokal disiapkan pada CTX-001. Remote belum dihubungkan dan tidak ada push. Revisi awal dapat ditemukan dengan `git log --oneline`; kondisi sebenarnya selalu diperiksa dengan Git, bukan disimpulkan dari teks ini.

## Lokasi penting

| Area | Lokasi |
| --- | --- |
| Alur publikasi | `app/Http/Controllers/PublicationController.php` |
| Alur literasi | `app/Http/Controllers/LiteracyAssignmentController.php`, `app/Http/Controllers/LiteracySubmissionController.php` |
| Penilaian dan apresiasi | `app/Http/Controllers/EvaluationController.php`, `tests/Feature/EvaluationWorkflowTest.php` |
| Monitoring dan laporan | `app/Services/MonitoringReport.php`, `app/Http/Controllers/MonitoringController.php`, `tests/Feature/MonitoringWorkflowTest.php` |
| Album Drive | `app/Services/DriveAlbumService.php` |
| Validasi konten | `app/Http/Requests/SavePublicationRequest.php` |
| Tampilan | `resources/views`, `public/css/app.css`, `public/js/app.js` |
| Tes | `tests/Feature/SchoolApplicationTest.php`, `tests/Feature/DriveAlbumTest.php` |
| Data awal | `database/migrations`, `database/seeders` |
| Konfigurasi contoh | `.env.example` |
| Rancangan dan sumber | `instruction/sumber` |

Dokumen ini adalah ringkasan keadaan terkini. Aturan bisnis tetap berada pada rancangan; keputusan dan perubahan alasan berada pada DECISIONS.md.
