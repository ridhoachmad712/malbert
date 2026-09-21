# Status proyek

Diperbarui: 2026-09-21 oleh Codex, finalisasi LIT-001.

Fondasi dan alur literasi sudah diimplementasikan, diuji, dan diintegrasikan. Bukti terbaru: [HANDOFF_LIT_001.md](instruction/serah-terima/HANDOFF_LIT_001.md); fondasi terdahulu: [HANDOFF_FND_001.md](instruction/serah-terima/HANDOFF_FND_001.md).

Dokumentasi disatukan di [instruction](instruction/README.md). Catatan penataan terbaru: [DOC-002](instruction/serah-terima/HANDOFF_DOC_002.md). Fitur aplikasi tidak berubah.

Pembaruan dokumentasi DOC-001: catatan untuk pihak sekolah disimpan pada [Kebutuhan data dan informasi sekolah](instruction/sekolah/KEBUTUHAN_DATA_DAN_INFORMASI_SEKOLAH.md). Tidak ada perubahan fitur aplikasi; data sekolah masih menunggu pengumpulan.

## Kondisi produk

- Aplikasi Laravel Ruang Cerita berjalan lokal sebagai dasar TEBAK AKSI.
- Tersedia: akun admin/guru, publikasi kegiatan/karya/prestasi, draf/tinjauan/revisi/arsip, pencarian, profil/logo sekolah, foto terkompresi, tautan Drive, dan pencadangan.
- Impor folder Drive dan slider sudah memiliki kode dan tes, tetapi koneksi nyata belum selesai. Jangan mengklaim API sudah aktif. Proses Google Cloud terakhir tertahan persyaratan verifikasi dua langkah; status akun perlu diperiksa kembali bila melanjutkan.
- Tahun ajaran, pilihan semester berjalan, rombel, penugasan guru, data murid, impor CSV, verifikasi/status aktif, dan riwayat perpindahan sudah tersedia pada working tree. Akses guru mengikuti rombel saat ini. Filter daftar murid memang memakai kelas saat ini, bukan laporan historis.
- Pojok Baca publik, bahan bacaan internal, tugas per rombel, jurnal membaca, karya tulisan/tautan/foto/PDF, refleksi, tinjauan, revisi, persetujuan, dan riwayat snapshot sudah tersedia. Hasil dan berkas karya tetap privat serta tidak otomatis menjadi publikasi.
- Penilaian/poin, monitoring, laporan, pembinaan, dan pengaduan belum diimplementasikan.
- Data yang terlihat masih contoh. Belum deployment online. Penjadwalan cadangan pada server belum diaktifkan.
- Anti-slop diadopsi sebagai panduan lokal; audit menyeluruh UI lama belum dilakukan.

## Pekerjaan berikutnya

1. NIL-001: selesaikan keputusan terbuka yang diperlukan untuk observasi, nilai kemampuan, poin aktivitas, koreksi, dan apresiasi sebelum mengubah skema. Jangan menggabungkan nilai kemampuan dengan poin aktivitas.
2. DRV-001 dapat dilanjutkan terpisah bila akses Google Cloud tersedia.
3. OPS-001: menghubungkan remote privat agar akun/perangkat/cloud lain dapat mengambil revisi terbaru. Belum ada tujuan remote yang dipilih pengguna.

## Bukti dan batas verifikasi

Pengujian terbaru 2026-09-21: 57 tes / 398 assertions lulus, termasuk 9 tes literasi / 67 assertions dan tes regresi seluruh aplikasi. Pint, pemeriksaan sintaks JavaScript, daftar rute, dan git diff --check lulus. Tes literasi membuktikan persetujuan bacaan publik, snapshot periode, pembatasan rombel, kelengkapan jurnal/refleksi, revisi terlacak, karya privat, escaping, tipe berkas, dan pemulihan backup versi 3. QA visual browser lokal membuktikan hierarki, keadaan kosong, navigasi, dan tidak adanya overflow yang tampak pada viewport aplikasi 668 × 840; halaman pengelolaan dibuktikan melalui tes render, bukan sesi browser nyata. Tes Drive tetap memakai respons tiruan. Audit UI menyeluruh dan uji bersama sekolah belum dilakukan.

## Koordinasi

Tidak ada pekerja aktif setelah finalisasi LIT-001. Periksa TASKS.md dan status aktual sebelum mengambil tugas berikutnya.

Git lokal disiapkan pada CTX-001. Remote belum dihubungkan dan tidak ada push. Revisi awal dapat ditemukan dengan `git log --oneline`; kondisi sebenarnya selalu diperiksa dengan Git, bukan disimpulkan dari teks ini.

## Lokasi penting

| Area | Lokasi |
| --- | --- |
| Alur publikasi | `app/Http/Controllers/PublicationController.php` |
| Alur literasi | `app/Http/Controllers/LiteracyAssignmentController.php`, `app/Http/Controllers/LiteracySubmissionController.php` |
| Album Drive | `app/Services/DriveAlbumService.php` |
| Validasi konten | `app/Http/Requests/SavePublicationRequest.php` |
| Tampilan | `resources/views`, `public/css/app.css`, `public/js/app.js` |
| Tes | `tests/Feature/SchoolApplicationTest.php`, `tests/Feature/DriveAlbumTest.php` |
| Data awal | `database/migrations`, `database/seeders` |
| Konfigurasi contoh | `.env.example` |
| Rancangan dan sumber | `instruction/sumber` |

Dokumen ini adalah ringkasan keadaan terkini. Aturan bisnis tetap berada pada rancangan; keputusan dan perubahan alasan berada pada DECISIONS.md.
