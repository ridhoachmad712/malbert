# Status proyek

Diperbarui: 2026-09-21 oleh Codex, progres DRV-001.

Fondasi, literasi, penilaian/apresiasi, monitoring/laporan, serta inti publikasi terhubung bank karya sudah diimplementasikan, diuji, dan diintegrasikan. Bukti terbaru: [HANDOFF_PUB_001.md](instruction/serah-terima/HANDOFF_PUB_001.md); tahap sebelumnya: [HANDOFF_MON_001.md](instruction/serah-terima/HANDOFF_MON_001.md).

Dokumentasi disatukan di [instruction](instruction/README.md). Catatan penataan terbaru: [DOC-002](instruction/serah-terima/HANDOFF_DOC_002.md). Fitur aplikasi tidak berubah.

Pembaruan dokumentasi DOC-001: catatan untuk pihak sekolah disimpan pada [Kebutuhan data dan informasi sekolah](instruction/sekolah/KEBUTUHAN_DATA_DAN_INFORMASI_SEKOLAH.md). Tidak ada perubahan fitur aplikasi; data sekolah masih menunggu pengumpulan.

## Kondisi produk

- Aplikasi Laravel Ruang Cerita berjalan lokal sebagai dasar TEBAK AKSI.
- Tersedia: akun admin/guru, publikasi kegiatan/karya/prestasi, draf/tinjauan/revisi/arsip, pencarian, profil/logo sekolah, foto terkompresi, tautan Drive, dan pencadangan.
- Impor folder Drive dan slider sudah memiliki kode dan tes. Google Drive API dan key yang dibatasi ke Drive API sudah aktif pada lingkungan lokal ini; koneksi nyata mencapai API dan menerima respons `notFound` untuk ID folder uji fiktif. Album folder nyata belum terbukti sampai tersedia tautan folder publik berisi foto/video.
- Tahun ajaran, pilihan semester berjalan, rombel, penugasan guru, data murid, impor CSV, verifikasi/status aktif, dan riwayat perpindahan sudah tersedia pada working tree. Akses guru mengikuti rombel saat ini. Filter daftar murid memang memakai kelas saat ini, bukan laporan historis.
- Pojok Baca publik, bahan bacaan internal, tugas per rombel, jurnal membaca, karya tulisan/tautan/foto/PDF, refleksi, tinjauan, revisi, persetujuan, dan riwayat snapshot sudah tersedia. Hasil tetap privat dan tidak otomatis menjadi publikasi.
- Bank karya hanya menampilkan hasil yang disetujui. Satu karya dapat disiapkan menjadi satu draf publikasi dengan hubungan sumber dan nomor revisi; hanya karya terpilih yang disalin, sedangkan jurnal, refleksi, umpan balik, nilai, NIS, dan NISN tidak masuk ke konten publik. Kredit nama murid memerlukan konfirmasi izin khusus. Revisi draf mempertahankan versi publik terakhir sampai admin menyetujui ulang.
- Penilaian dari rubrik enam indikator, nilai dan kategori otomatis, umpan balik, transaksi poin aktivitas, tingkatan apresiasi per periode, pencegahan poin ganda, serta koreksi bernalar dan terlacak sudah tersedia. Admin wajib menyimpan konfigurasi aturan sekolah sebelum modul digunakan.
- Monitoring per kelas/periode, perkembangan bulanan murid, konfigurasi capaian 80%, pembedaan data belum dinilai, laporan bulanan cetak, dan ekspor CSV sudah tersedia. Angka menggunakan tanggal kegiatan sumber dan akses guru mengikuti rombel.
- Pembinaan dan pengaduan belum diimplementasikan dan ditunda pengguna karena belum mendesak.
- Data yang terlihat masih contoh. Belum deployment online. Penjadwalan cadangan pada server belum diaktifkan.
- Anti-slop diadopsi sebagai panduan lokal; audit menyeluruh UI lama belum dilakukan.

## Pekerjaan berikutnya

1. DRV-001 menunggu tautan folder Drive publik berisi foto/video untuk menguji impor, tampilan tanpa login Google, dan batas akses; tahap 6 keseluruhan belum selesai sampai bukti itu lulus.
2. OPS-001: menghubungkan remote privat agar akun/perangkat/cloud lain dapat mengambil revisi terbaru. Belum ada tujuan remote yang dipilih pengguna.
3. REL-001: uji bersama sekolah, pemulihan cadangan, konfigurasi hosting/domain, akun nyata, dan aktivasi jadwal cadangan.

KAS-001 ditunda atas keputusan pengguna pada 2026-09-21 karena belum mendesak. Lanjutkan hanya bila pengguna mengubah prioritas dan sekolah sudah menetapkan aturan penanganan serta akses data sensitif.

## Bukti dan batas verifikasi

Pengujian aplikasi terbaru 2026-09-21: 77 tes / 541 assertions lulus, termasuk 4 tes publikasi terhubung / 44 assertions dan regresi seluruh aplikasi. Pint, daftar 68 rute, dan git diff --check lulus. Tes publikasi membuktikan kelayakan hasil, hubungan sumber, pembatasan data internal, izin identitas, salinan aset, akses publik, versi terbit yang tetap, dan backup versi 6. QA browser bank karya lulus pada desktop dan 390 × 844 tanpa error konsol; database lokal tidak memiliki karya disetujui sehingga keadaan berisi dibuktikan lewat tes render. Pada DRV-001, koneksi tanpa cookie ke Google Drive API sudah dilakukan dengan key lokal dan menghasilkan `404/notFound` untuk ID fiktif, yang membuktikan endpoint dapat dicapai tetapi belum membuktikan album nyata. Tes perilaku album masih memakai respons tiruan. Audit UI menyeluruh dan uji bersama sekolah belum dilakukan.

Saat pemeriksaan migrasi, perintah CLI `migrate:fresh --env=testing` ternyata masih menunjuk database MySQL lokal dan meresetnya sebelum migrasi gagal karena nama indeks terlalu panjang. Nama indeks sudah diperpendek, tabel parsial kosong dihapus, migrasi dijalankan ulang, lalu database dipulihkan dari cadangan terbaru yang tersedia `school-20260919-164003-UtkKg9.zip`. Isi yang pulih sesuai arsip: 2 pengguna, 7 publikasi, 1 pengaturan sekolah, tanpa tahun ajaran atau murid. QA selanjutnya memakai SQLite terpisah dengan target database eksplisit. Jangan memakai `migrate:fresh` CLI tanpa memeriksa target database aktual.

## Koordinasi

DRV-001 sedang dimiliki Codex lokal. Tidak ada pekerja paralel; lihat HANDOFF_DRV_001 sebelum melanjutkan atau mengambil alih.

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
