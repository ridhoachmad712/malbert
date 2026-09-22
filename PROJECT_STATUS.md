# Status proyek

Diperbarui: 2026-09-22 oleh Codex, rekomendasi hosting/domain REL-001.

Fondasi, literasi, penilaian/apresiasi, monitoring/laporan, serta publikasi terhubung bank karya dan album Drive sudah diimplementasikan, diuji, dan diintegrasikan. Audit antarmuka selesai. Persiapan lokal REL-001 juga tersedia, tetapi peluncuran tetap menunggu keputusan dan UAT sekolah; lihat [HANDOFF_REL_001.md](instruction/serah-terima/HANDOFF_REL_001.md).

Dokumentasi disatukan di [instruction](instruction/README.md). Catatan penataan terbaru: [DOC-002](instruction/serah-terima/HANDOFF_DOC_002.md). Fitur aplikasi tidak berubah.

Pembaruan dokumentasi DOC-001: catatan untuk pihak sekolah disimpan pada [Kebutuhan data dan informasi sekolah](instruction/sekolah/KEBUTUHAN_DATA_DAN_INFORMASI_SEKOLAH.md). Tidak ada perubahan fitur aplikasi; data sekolah masih menunggu pengumpulan.

## Kondisi produk

- Aplikasi Laravel Ruang Cerita berjalan lokal sebagai dasar TEBAK AKSI.
- Tersedia: akun admin/guru, publikasi kegiatan/karya/prestasi, draf/tinjauan/revisi/arsip, pencarian, profil/logo sekolah, foto terkompresi, tautan Drive, dan pencadangan.
- Impor folder Drive dan slider aktif pada lingkungan lokal ini. Folder publik nyata berisi 8 foto berhasil dibaca, seluruh pratinjau merespons tanpa cookie Google, dan slider publik berhasil menampilkan serta berpindah antar-media.
- Tahun ajaran, pilihan semester berjalan, rombel, penugasan guru, data murid, impor CSV, verifikasi/status aktif, dan riwayat perpindahan sudah tersedia pada working tree. Akses guru mengikuti rombel saat ini. Filter daftar murid memang memakai kelas saat ini, bukan laporan historis.
- Pojok Baca publik, bahan bacaan internal, tugas per rombel, jurnal membaca, karya tulisan/tautan/foto/PDF, refleksi, tinjauan, revisi, persetujuan, dan riwayat snapshot sudah tersedia. Hasil tetap privat dan tidak otomatis menjadi publikasi.
- Bank karya hanya menampilkan hasil yang disetujui. Satu karya dapat disiapkan menjadi satu draf publikasi dengan hubungan sumber dan nomor revisi; hanya karya terpilih yang disalin, sedangkan jurnal, refleksi, umpan balik, nilai, NIS, dan NISN tidak masuk ke konten publik. Kredit nama murid memerlukan konfirmasi izin khusus. Revisi draf mempertahankan versi publik terakhir sampai admin menyetujui ulang.
- Penilaian dari rubrik enam indikator, nilai dan kategori otomatis, umpan balik, transaksi poin aktivitas, tingkatan apresiasi per periode, pencegahan poin ganda, serta koreksi bernalar dan terlacak sudah tersedia. Admin wajib menyimpan konfigurasi aturan sekolah sebelum modul digunakan.
- Monitoring per kelas/periode, perkembangan bulanan murid, konfigurasi capaian 80%, pembedaan data belum dinilai, laporan bulanan cetak, dan ekspor CSV sudah tersedia. Angka menggunakan tanggal kegiatan sumber dan akses guru mengikuti rombel.
- Pembinaan dan pengaduan belum diimplementasikan dan ditunda pengguna karena belum mendesak.
- Data yang terlihat masih contoh. Belum deployment online. Penjadwalan cadangan pada server belum diaktifkan.
- Audit UI berbasis anti-slop selesai pada 21 rute representatif publik/admin dan 10 rute guru, masing-masing pada desktop dan ponsel. Kontras, overflow, sasaran sentuh, navigasi keyboard, keadaan kosong/peringatan/error, semantik dekorasi, dan konsol browser sudah diperiksa.
- Template lingkungan produksi, pemeriksaan `school:release-check`, panduan deployment/restore, lembar UAT sekolah, dan perbandingan hosting/domain sudah tersedia. Hostinger Unlimited direkomendasikan secara bersyarat untuk rilis awal; staging tetap harus membuktikan Laravel 13, PHP 8.3 CLI, cron setiap menit, document root, dan restore. Pemulihan backup nyata lulus pada database terisolasi dengan 17 tabel tanpa selisih. Lingkungan aktif tetap lokal/debug dan masih memakai data contoh.

## Pekerjaan berikutnya

1. REL-001: putuskan rekomendasi Hostinger Unlimited atau hosting/domain sekolah yang sudah ada, validasi staging, isi identitas serta akun nyata, jalankan UAT bersama sekolah, aktifkan cron dan salinan backup di luar server, lalu pastikan `school:release-check` seluruhnya `SIAP`.
2. OPS-001: menghubungkan remote privat agar akun/perangkat/cloud lain dapat mengambil revisi terbaru. Belum ada tujuan remote yang dipilih pengguna.
3. Menyesuaikan data contoh menjadi data sekolah yang telah diverifikasi sebelum penggunaan nyata.

KAS-001 ditunda atas keputusan pengguna pada 2026-09-21 karena belum mendesak. Lanjutkan hanya bila pengguna mengubah prioritas dan sekolah sudah menetapkan aturan penanganan serta akses data sensitif.

## Bukti dan batas verifikasi

Pengujian aplikasi terbaru 2026-09-21: 79 tes / 548 assertions lulus. Pint, sintaks JavaScript, 68 rute, `composer check-platform-reqs`, scheduler backup pukul 02.00, endpoint `/up` HTTP 200, dan diff check lulus. Restore backup nyata ke SQLite/penyimpanan terisolasi berhasil: migrasi dan restore keluar 0, jumlah seluruh 17 tabel cocok, serta cadangan saat itu memang tidak memiliki berkas media lokal. Jalur media backup tetap dicakup tes otomatis. `school:release-check` dengan sengaja masih melaporkan tujuh item BELUM pada lingkungan lokal: mode produksi, debug, HTTPS, cookie aman, enkripsi sesi, profil nyata, dan akun demo. UAT bersama sekolah serta server/domain nyata belum tersedia.

Saat pemeriksaan migrasi, perintah CLI `migrate:fresh --env=testing` ternyata masih menunjuk database MySQL lokal dan meresetnya sebelum migrasi gagal karena nama indeks terlalu panjang. Nama indeks sudah diperpendek, tabel parsial kosong dihapus, migrasi dijalankan ulang, lalu database dipulihkan dari cadangan terbaru yang tersedia `school-20260919-164003-UtkKg9.zip`. Isi yang pulih sesuai arsip: 2 pengguna, 7 publikasi, 1 pengaturan sekolah, tanpa tahun ajaran atau murid. QA selanjutnya memakai SQLite terpisah dengan target database eksplisit. Jangan memakai `migrate:fresh` CLI tanpa memeriksa target database aktual.

## Koordinasi

Tidak ada pekerja aktif. REL-001 berstatus BLOCKED setelah rekomendasi hosting selesai; lanjutkan ketika pengguna menetapkan hosting/domain, staging tersedia, dan sekolah siap menjalankan UAT dengan akun/data nyata.

Git lokal disiapkan pada CTX-001. Remote belum dihubungkan dan tidak ada push. Revisi awal dapat ditemukan dengan `git log --oneline`; kondisi sebenarnya selalu diperiksa dengan Git, bukan disimpulkan dari teks ini.

## Lokasi penting

| Area | Lokasi |
| --- | --- |
| Alur publikasi | `app/Http/Controllers/PublicationController.php` |
| Integrasi karya ke publikasi | `app/Services/WorkPublicationService.php`, `tests/Feature/PublicationWorkflowTest.php` |
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
