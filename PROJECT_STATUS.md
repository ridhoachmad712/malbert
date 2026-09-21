# Status proyek

Diperbarui: 2026-09-21 oleh Codex, finalisasi FND-001.

Fondasi sudah diimplementasikan, diuji, dan diintegrasikan sebagai checkpoint FND-001. Bukti terbaru: [HANDOFF_FND_001.md](instruction/serah-terima/HANDOFF_FND_001.md).

Dokumentasi disatukan di [instruction](instruction/README.md). Catatan penataan terbaru: [DOC-002](instruction/serah-terima/HANDOFF_DOC_002.md). Fitur aplikasi tidak berubah.

Pembaruan dokumentasi DOC-001: catatan untuk pihak sekolah disimpan pada [Kebutuhan data dan informasi sekolah](instruction/sekolah/KEBUTUHAN_DATA_DAN_INFORMASI_SEKOLAH.md). Tidak ada perubahan fitur aplikasi; data sekolah masih menunggu pengumpulan.

## Kondisi produk

- Aplikasi Laravel Ruang Cerita berjalan lokal sebagai dasar TEBAK AKSI.
- Tersedia: akun admin/guru, publikasi kegiatan/karya/prestasi, draf/tinjauan/revisi/arsip, pencarian, profil/logo sekolah, foto terkompresi, tautan Drive, dan pencadangan.
- Impor folder Drive dan slider sudah memiliki kode dan tes, tetapi koneksi nyata belum selesai. Jangan mengklaim API sudah aktif. Proses Google Cloud terakhir tertahan persyaratan verifikasi dua langkah; status akun perlu diperiksa kembali bila melanjutkan.
- Tahun ajaran, pilihan semester berjalan, rombel, penugasan guru, data murid, impor CSV, verifikasi/status aktif, dan riwayat perpindahan sudah tersedia pada working tree. Akses guru mengikuti rombel saat ini. Filter daftar murid memang memakai kelas saat ini, bukan laporan historis.
- Jurnal, tugas, penilaian/poin, monitoring, laporan, pembinaan, dan pengaduan belum diimplementasikan.
- Data yang terlihat masih contoh. Belum deployment online. Penjadwalan cadangan pada server belum diaktifkan.
- Anti-slop diadopsi sebagai panduan lokal; audit menyeluruh UI lama belum dilakukan.

## Pekerjaan berikutnya

1. LIT-001: tetapkan kontrak data bacaan, tugas, jurnal, karya, refleksi dan pemeriksaan, lalu implementasikan alur pertama. Catatan literasi harus menyimpan periode pada data sumbernya; jangan menghitung periode historis dari pilihan semester berjalan yang dapat berubah.
2. DRV-001 dapat dilanjutkan terpisah bila akses Google Cloud tersedia.
3. OPS-001: menghubungkan remote privat agar akun/perangkat/cloud lain dapat mengambil revisi terbaru. Belum ada tujuan remote yang dipilih pengguna.

## Bukti dan batas verifikasi

Pengujian terbaru 2026-09-21: 48 tes / 331 assertions lulus, termasuk 26 tes fondasi / 174 assertions. Pint, pemeriksaan sintaks JavaScript, daftar rute, dan git diff --check lulus. Tes Drive tetap memakai respons tiruan. Browser QA dengan SQLite terpisah membuktikan tambah/edit/verifikasi/nonaktif/pindah murid, tahun ajaran dan semester aktif, rombel serta penugasan, impor CSV gagal/berhasil, pembatasan admin/guru, pencarian/keadaan kosong, dan lebar layar 390 serta 1280 piksel. Audit UI menyeluruh dan uji bersama sekolah belum dilakukan.

## Koordinasi

Tidak ada pekerja aktif setelah finalisasi FND-001. Periksa TASKS.md dan status aktual sebelum mengambil tugas berikutnya.

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
