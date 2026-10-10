# Status proyek

Diperbarui: 2026-10-10 oleh Codex, diagnosis deployment Hostinger.

Fondasi, literasi, penilaian/apresiasi, monitoring/laporan, serta publikasi terhubung bank karya dan album Drive sudah diimplementasikan, diuji, dan diintegrasikan. Audit antarmuka selesai. Persiapan lokal REL-001 juga tersedia, tetapi peluncuran tetap menunggu keputusan dan UAT sekolah; lihat [HANDOFF_REL_001.md](instruction/serah-terima/HANDOFF_REL_001.md).

Dokumentasi disatukan di [instruction](instruction/README.md). Catatan penataan terbaru: [DOC-002](instruction/serah-terima/HANDOFF_DOC_002.md). Fitur aplikasi tidak berubah.

Pembaruan dokumentasi DOC-001: catatan untuk pihak sekolah disimpan pada [Kebutuhan data dan informasi sekolah](instruction/sekolah/KEBUTUHAN_DATA_DAN_INFORMASI_SEKOLAH.md). Tidak ada perubahan fitur aplikasi; data sekolah masih menunggu pengumpulan.

## Kondisi produk

- Aplikasi Laravel **AksiKita — Portal Literasi, Karya, dan Karakter Siswa** berjalan lokal sebagai dasar TEBAK AKSI.
- Beranda publik kini menempatkan cerita pilihan, indeks karya/kegiatan/prestasi, dan sampul publikasi sebagai fokus portofolio. Halaman cerita dan profil sekolah memakai tipografi serta komposisi editorial yang selaras. Navigasi publik diringkas menjadi Portofolio, Pojok Baca, dan Tentang Sekolah. Teks serta warna utama tetap mengikuti pengaturan admin.
- Pemeriksaan UI-004 pada 2026-10-10: 21 tes / 160 assertions dan kompilasi Blade lulus; beranda, cerita, dan profil pada lebar 390 px tidak meluap horizontal; beranda juga diperiksa pada 1440 px. Filter kategori, pencarian tanpa hasil, menu ponsel, serta tautan cerita pilihan diuji di browser lokal. Publikasi lokal yang tampil saat pemeriksaan masih data contoh, sehingga sampul menggunakan penanda contoh.
- Branding AksiKita sudah tampil pada header, metadata, halaman utama, halaman masuk, footer, dan laporan cetak; nama sekolah tetap ditampilkan sebagai identitas instansi.
- Admin memiliki halaman **Tampilan & Konten** untuk mengubah nama aplikasi, nama penjelas, palet tujuh warna, teks halaman utama/login/footer, gambar utama/login/profil, serta favicon. Pratinjau berubah sebelum disimpan, warna teks diperiksa kontrasnya, dan tampilan dapat dikembalikan ke bawaan tanpa mengubah data sekolah.
- Halaman pengelolaan memiliki topbar ringkas dan sidebar berkelompok untuk ringkasan, pembelajaran, publikasi, administrasi, serta akun. Navigasi administrasi hanya tampil untuk admin; pada ponsel menu menjadi drawer. Footer dan strip pratinjau milik situs publik tidak dirender di ruang pengelolaan.
- Dasbor kini mendahulukan pekerjaan literasi: rombel, tugas aktif, hasil menunggu tinjauan, dan hasil disetujui yang belum dinilai. Guru hanya melihat data rombel yang ditugaskan, serta menerima penjelasan jika belum memiliki penugasan. Admin juga melihat antrean publikasi. Menu menempatkan Pembelajaran sebelum Publikasi. Daftar cerita lengkap dengan pencarian/filter dipindahkan ke `/kelola/konten` dan tampil sebagai kartu pada ponsel. Bukti UI-005 ada di [serah terima](instruction/serah-terima/HANDOFF_UI_005.md).
- Tersedia: akun admin/guru, publikasi kegiatan/karya/prestasi, draf/tinjauan/revisi/arsip, pencarian, profil/logo sekolah, foto terkompresi, tautan Drive, dan pencadangan.
- Impor folder Drive dan slider aktif pada lingkungan lokal ini. Folder publik nyata berisi 8 foto berhasil dibaca, seluruh pratinjau merespons tanpa cookie Google, dan slider publik berhasil menampilkan serta berpindah antar-media.
- Tahun ajaran, pilihan semester berjalan, rombel, penugasan guru, data murid, impor CSV, verifikasi/status aktif, dan riwayat perpindahan sudah tersedia pada working tree. Akses guru mengikuti rombel saat ini. Filter daftar murid memang memakai kelas saat ini, bukan laporan historis.
- Pojok Baca publik, bahan bacaan internal, tugas per rombel, jurnal membaca, karya tulisan/tautan/foto/PDF, refleksi, tinjauan, revisi, persetujuan, dan riwayat snapshot sudah tersedia. Hasil tetap privat dan tidak otomatis menjadi publikasi.
- Bank karya hanya menampilkan hasil yang disetujui. Satu karya dapat disiapkan menjadi satu draf publikasi dengan hubungan sumber dan nomor revisi; hanya karya terpilih yang disalin, sedangkan jurnal, refleksi, umpan balik, nilai, NIS, dan NISN tidak masuk ke konten publik. Kredit nama murid memerlukan konfirmasi izin khusus. Revisi draf mempertahankan versi publik terakhir sampai admin menyetujui ulang.
- Penilaian dari rubrik enam indikator, nilai dan kategori otomatis, umpan balik, transaksi poin aktivitas, tingkatan apresiasi per periode, pencegahan poin ganda, serta koreksi bernalar dan terlacak sudah tersedia. Admin wajib menyimpan konfigurasi aturan sekolah sebelum modul digunakan.
- Monitoring per kelas/periode, perkembangan bulanan murid, konfigurasi capaian 80%, pembedaan data belum dinilai, laporan bulanan cetak, dan ekspor CSV sudah tersedia. Angka menggunakan tanggal kegiatan sumber dan akses guru mengikuti rombel.
- Pembinaan dan pengaduan belum diimplementasikan dan ditunda pengguna karena belum mendesak.
- Profil lokal sudah bernama SD Inpres Malbert, tetapi masih ditandai sebagai pratinjau dan identitas lengkap belum diberikan pada sesi. Aplikasi kini dapat dibuka pada domain sementara Hostinger; profil nyata dan pemeriksaan rilis belum lengkap. Penjadwalan cadangan pada server belum diaktifkan.
- Audit UI berbasis anti-slop selesai pada 21 rute representatif publik/admin dan 10 rute guru, masing-masing pada desktop dan ponsel. Kontras, overflow, sasaran sentuh, navigasi keyboard, keadaan kosong/peringatan/error, semantik dekorasi, dan konsol browser sudah diperiksa.
- Template lingkungan produksi, pemeriksaan `school:release-check`, panduan deployment/restore, lembar UAT sekolah, dan perbandingan hosting/domain sudah tersedia. Penyedia deployment awal diketahui Hostinger dengan domain sementara yang dicatat di bawah; domain resmi sekolah belum dicatat. Form profil kini memiliki field terpisah untuk NPSN, email, dan telepon resmi. Pemulihan backup nyata lulus pada database terisolasi dengan 17 tabel tanpa selisih. Lingkungan lokal masih debug dan memakai data contoh.
- OPS-001 selesai: kode pada `main` tersinkron ke [GitHub malbert](https://github.com/ridhoachmad712/malbert), dengan repositori publik sesuai pilihan pengguna. `.env` dan perubahan lokal yang belum di-commit tidak ikut push.
- Deployment awal pada `https://mediumaquamarine-whale-776973.hostingersite.com` menaruh repo langsung di `public_html`. Semula `/` HTTP 403, `/public/` HTTP 500, dan `/public/css/app.css` HTTP 200. Setelah `.htaccess` akar dari commit `4362fda` tertarik Hostinger, pemeriksaan eksternal 2026-10-10 menunjukkan `/` dan `/up` HTTP 500, `/css/app.css` HTTP 200, `/composer.json` HTTP 404, `/.env` dan `/storage/logs/laravel.log` HTTP 403. Jadi 403 root tertangani, tetapi aplikasi belum berjalan. Log produksi yang diberikan pengguna menunjukkan `APP_KEY` kosong dan koneksi bawaan `sqlite` mengarah ke berkas database yang tidak ada. Perlu `.env` produksi MySQL di server, key instalasi baru, migrasi, serta pemeriksaan ulang; akses server belum tersedia pada sesi ini.
- Setelah pengguna menyiapkan `.env`, MySQL, `APP_KEY`, dan migrasi di Hostinger, pemeriksaan HTTP 2026-10-10 menunjukkan `/` dan `/up` HTTP 200, `/css/app.css` HTTP 200, `/composer.json` HTTP 404, serta `.env` dan log HTTP 403. `school:release-check` di server dilaporkan pengguna: 9 `SIAP`, 4 `BELUM` (Google Drive, profil sekolah bukan contoh, admin aktif, guru aktif). Perintah `school:create-admin` ditambahkan untuk bootstrap admin pertama secara interaktif tanpa menampilkan kata sandi; perubahan ini belum dipasang/diuji di server saat catatan dibuat.
- Pengguna berhasil menjalankan `school:create-admin` di Hostinger. Setelah itu Chrome menampilkan peringatan Google Safe Browsing bahwa domain sementara berpotensi phishing. Penyebab belum diketahui; penggunaan login dan impor data nyata ditahan sampai Malware Scanner Hostinger serta laporan Security Issues diperiksa. Perintah masuk yang benar adalah `/masuk`; tautan `/login` yang sebelumnya diberikan dalam percakapan keliru.

## Pekerjaan berikutnya

1. REL-001: selidiki dan selesaikan peringatan Safe Browsing pada domain sementara sebelum login lanjutan atau impor data murid. Setelah aman, verifikasi admin, isi profil sekolah dan akun guru, konfigurasi Google Drive, lalu ulangi pemeriksaan rilis. Setelah itu validasi staging, jalankan UAT bersama sekolah, aktifkan cron dan salinan backup di luar server, lalu pastikan `school:release-check` seluruhnya `SIAP`.
2. Menyesuaikan data contoh menjadi data sekolah yang telah diverifikasi sebelum penggunaan nyata.

KAS-001 ditunda atas keputusan pengguna pada 2026-09-21 karena belum mendesak. Lanjutkan hanya bila pengguna mengubah prioritas dan sekolah sudah menetapkan aturan penanganan serta akses data sensitif.

## Bukti dan batas verifikasi

Pengujian aplikasi terbaru 2026-09-23: 86 tes / 608 assertions, Pint, sintaks JavaScript, dan kompilasi Blade lulus. Dasbor serta halaman Tampilan & Konten diperiksa pada viewport ponsel; topbar tetap ringkas, drawer menu dapat dibuka, tidak terjadi overflow horizontal, dan footer publik tidak muncul. Panel Tampilan & Konten sebelumnya juga lulus pada desktop dan viewport 390 px; pratinjau langsung terbukti berubah tanpa menyimpan. Bukti kesiapan platform sebelumnya tetap berlaku: `composer check-platform-reqs`, scheduler backup pukul 02.00, endpoint `/up` HTTP 200 lokal, serta restore 17 tabel tanpa selisih. `school:release-check` tetap belum dapat lulus pada lingkungan lokal sampai konfigurasi produksi, identitas lengkap, dan akun demo ditangani. UAT bersama sekolah serta verifikasi server/domain yang berhasil masih belum tersedia.

Saat pemeriksaan migrasi, perintah CLI `migrate:fresh --env=testing` ternyata masih menunjuk database MySQL lokal dan meresetnya sebelum migrasi gagal karena nama indeks terlalu panjang. Nama indeks sudah diperpendek, tabel parsial kosong dihapus, migrasi dijalankan ulang, lalu database dipulihkan dari cadangan terbaru yang tersedia `school-20260919-164003-UtkKg9.zip`. Isi yang pulih sesuai arsip: 2 pengguna, 7 publikasi, 1 pengaturan sekolah, tanpa tahun ajaran atau murid. QA selanjutnya memakai SQLite terpisah dengan target database eksplisit. Jangan memakai `migrate:fresh` CLI tanpa memeriksa target database aktual.

## Koordinasi

Tidak ada pekerja aktif. REL-001 berstatus BLOCKED oleh peringatan Safe Browsing pada domain sementara Hostinger; investigasi diperlukan sebelum data nyata diimpor. Pengisian profil, integrasi Drive, backup server, dan UAT belum selesai. Rujuk checkpoint deployment pada [serah terima REL-001](instruction/serah-terima/HANDOFF_REL_001.md).

Git lokal disiapkan pada CTX-001. Remote `origin` terhubung ke repositori GitHub yang dipilih pengguna. `main` pertama kali didorong pada 2026-10-10; visibilitas `PUBLIC` dikonfirmasi pengguna. Perubahan lokal pengguna yang belum di-commit tidak ikut push. Kondisi sebenarnya selalu diperiksa dengan Git, bukan disimpulkan dari teks ini.

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
| Pengaturan tampilan & konten | `app/Http/Controllers/AppearanceController.php`, `resources/views/manage/appearance.blade.php`, `tests/Feature/AppearanceSettingsTest.php` |
| Tes | `tests/Feature/SchoolApplicationTest.php`, `tests/Feature/DriveAlbumTest.php` |
| Data awal | `database/migrations`, `database/seeders` |
| Konfigurasi contoh | `.env.example` |
| Rancangan dan sumber | `instruction/sumber` |

Dokumen ini adalah ringkasan keadaan terkini. Aturan bisnis tetap berada pada rancangan; keputusan dan perubahan alasan berada pada DECISIONS.md.
