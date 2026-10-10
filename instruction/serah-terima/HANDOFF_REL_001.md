# Serah terima REL-001

## Checkpoint deployment Hostinger — 2026-10-10

- Domain sementara `mediumaquamarine-whale-776973.hostingersite.com` menempatkan seluruh proyek di `public_html`. Pemeriksaan HTTP sebelum revisi: `/` = 403, `/public/` = 500, `/public/css/app.css` = 200. Ini menunjukkan web root belum mengarah ke entry point Laravel.
- Log produksi yang diberikan pengguna menunjukkan `MissingAppKeyException` dan koneksi `sqlite` ke `database/database.sqlite` yang tidak ada. Keduanya merupakan konfigurasi server; tidak ada rahasia yang dimasukkan ke Git.
- `.htaccess` akar dari commit `4362fda` sudah tertarik ke Hostinger: pemeriksaan HTTP ulang menunjukkan `/` dan `/up` = 500, `/css/app.css` = 200, `/composer.json` = 404, sedangkan `/.env` dan `/storage/logs/laravel.log` = 403. Ini membuktikan jalur web root berubah sesuai maksud, tetapi aplikasi belum berjalan. Panduan peluncuran memuat langkah instalasi baru dengan `.env` produksi MySQL, `APP_KEY`, pembersihan cache konfigurasi, dan migrasi.
- Status REL-001 tetap BLOCKED. Langkah berikutnya: atur `.env` dan database pada server, uji `/` dan `/up`, lalu lanjutkan pemeriksaan rilis dan UAT. Jangan memakai `migrate:fresh` atau menaruh `.env` di repo.

- Tanggal/waktu/zona: 2026-09-23 Asia/Makassar
- Agen/sesi: Codex lokal
- Status: BLOCKED
- Tujuan dan kriteria penerimaan: uji bersama guru pada ponsel, perbaikan alur, pemulihan cadangan, hosting/domain, akun sebenarnya, dan aktivasi jadwal cadangan. Selesai hanya jika sekolah telah mencoba alur utama, restore terbukti, konfigurasi produksi siap, serta akun/data contoh ditangani.
- Cabang, revisi dasar, revisi kode yang diuji: `main`, dasar `c7c3306`, working tree branding AksiKita sebelum commit.
- Commit hasil atau cara menemukannya: commit checkpoint REL-001 pada `main`; lihat `git log -1 -- instruction/serah-terima/HANDOFF_REL_001.md`.
- Pemilik selanjutnya: belum ditugaskan; lanjutkan setelah pengguna memberikan nilai identitas serta nama penyedia/domain yang telah dipilih.

## Hasil dan berkas berubah

- `school:release-check` memeriksa lingkungan produksi, debug, HTTPS, APP_KEY, keamanan sesi, migrasi, penyimpanan privat, jadwal backup, key Drive, profil sekolah, akun demo, serta ketersediaan admin/guru aktif.
- `.env.production.example` menyediakan nilai aman tanpa rahasia untuk lingkungan produksi.
- `instruction/operasional/PANDUAN_PELUNCURAN.md` memuat kebutuhan server, deployment, cron, backup/restore, pemeriksaan domain, dan batas peluncuran.
- `instruction/operasional/UAT_REL_001.md` memuat 18 skenario penerimaan untuk pengunjung, admin, guru, dan petugas server.
- `phpunit.xml` memakai jalur cache khusus tes. Ini mencegah proses tes membaca cache konfigurasi lokal/produksi dan menyentuh database yang salah.
- `.gitignore` mengizinkan template produksi dilacak tanpa mengizinkan `.env` produksi sebenarnya.
- `instruction/operasional/PILIHAN_HOSTING_REL_001.md` membandingkan pilihan terkelola dan VPS, menetapkan Hostinger Unlimited sebagai rekomendasi bersyarat, menjelaskan pilihan domain `.sch.id`, dan memberi enam pemeriksaan sebelum pembayaran.
- Profil sekolah menyimpan NPSN, email resmi, dan telepon resmi sebagai field terpisah. Form admin memvalidasi ketiganya, profil publik menampilkannya, dan `school:release-check` mensyaratkannya sebelum rilis.
- Nama aplikasi ditetapkan menjadi AksiKita dengan nama penjelas “Portal Literasi, Karya, dan Karakter Siswa”. Branding diterapkan pada header, metadata, halaman utama, halaman masuk, footer, laporan cetak, konfigurasi lokal, dan template produksi.

## Bukti pemeriksaan

| Perintah/interaksi | Hasil | Batas pemeriksaan |
| --- | --- | --- |
| `php artisan school:backup --no-interaction` | PASS; membuat `school-20260921-222955-YWREH7.zip` | Database lokal sebelum simulasi |
| Migrasi dan `school:backup --restore=...` pada SQLite/penyimpanan terisolasi | PASS; exit 0/0, 17 tabel diperiksa, 0 selisih | Cadangan nyata lokal; tidak ada media lokal di arsip saat itu |
| Tes backup yang sudah ada | PASS dalam suite; mencakup record dan media tiruan | SQLite in-memory dan storage fake |
| `php artisan test --compact` | PASS, 79 tes / 548 assertions | Regresi aplikasi |
| `vendor/bin/pint --dirty --format agent` | PASS | PHP yang berubah |
| `composer check-platform-reqs` | PASS | PHP 8.3.28 dan ekstensi lingkungan lokal |
| `php artisan route:list --except-vendor` | PASS, 68 rute | Registrasi rute |
| `php artisan schedule:list` | PASS; `school:backup` setiap hari pukul 02.00 | Definisi scheduler, bukan bukti cron server aktif |
| `GET /up` | PASS, HTTP 200 | Server lokal |
| Cache konfigurasi lokal aktif lalu tes readiness | PASS; tes tetap memakai SQLite in-memory | Isolasi cache tes baru |
| Verifikasi setelah pemulihan database lokal | PASS; 17 tabel cocok, termasuk 2 pengguna, 7 publikasi, 1 profil sekolah | Pemulihan dari backup pra-uji |
| `php artisan school:release-check` pada lokal | EXPECTED BLOCKED; 7 pemeriksaan BELUM | Lingkungan lokal memang bukan produksi |
| `php artisan test --compact` (2026-09-23) | PASS, 80 tes / 555 assertions | Regresi setelah penambahan field identitas |
| `vendor/bin/pint --dirty --format agent` (2026-09-23) | PASS | PHP yang berubah |
| Profil publik pada browser desktop dan viewport ponsel (2026-09-23) | PASS; identitas/placeholder terbaca tanpa overflow | Profil publik; form admin dicakup tes fitur, belum diisi data nyata |
| `php artisan test --compact` setelah branding (2026-09-23) | PASS, 80 tes / 557 assertions | Regresi aplikasi dan teks identitas utama |
| Halaman utama desktop/390 px dan halaman masuk 390 px (2026-09-23) | PASS; AksiKita serta nama penjelas tampil tanpa overflow | Pemeriksaan browser atas permukaan branding utama |

## Insiden verifikasi dan pemulihan

Tes dan `php artisan optimize` sempat dijalankan bersamaan. Cache konfigurasi lokal yang dibuat pada saat itu terbaca oleh proses tes, sehingga `RefreshDatabase` mengosongkan tabel aplikasi MySQL lokal. Backup yang dibuat tepat sebelum simulasi dipakai untuk memulihkan database. Pemeriksaan setelah restore menunjukkan 17 tabel tanpa selisih: 2 pengguna, 7 publikasi, dan 1 profil sekolah kembali sesuai arsip. Konfigurasi cache PHPUnit kemudian dipisahkan dan dibuktikan dengan menjalankan tes saat cache konfigurasi lokal aktif; data lokal tetap utuh. Jangan menjalankan perintah cache/optimize bersamaan dengan tes.

## Kondisi belum selesai

- Pengguna menyatakan hosting dan domain sudah dipilih, tetapi nama penyedia/domain tujuan belum dicatat dan layanannya belum dibuat. Deployment nyata, DNS, HTTPS, pembatasan IP key Drive, serta cron server belum dapat diuji.
- Pengguna menyatakan identitas sekolah sudah tersedia, tetapi nilai lengkapnya belum diberikan. Akun admin/guru nyata, data kelas/murid, dan konfigurasi aturan sekolah juga belum diberikan.
- Delapan belas skenario UAT masih `BELUM` karena memerlukan perwakilan sekolah dan perangkat yang benar-benar digunakan.
- Salinan backup di luar server dan bukti cron menghasilkan backup hari berikutnya belum tersedia.
- `school:release-check` lokal masih memiliki tujuh item `BELUM`, sesuai kondisi development.
- Remote Git privat belum tersedia; checkpoint hanya lokal.
- Rekomendasi hosting belum menjadi keputusan/pembelian. Kompatibilitas Laravel 13, PHP CLI, cron setiap menit, document root, dan restore masih harus dibuktikan pada staging penyedia yang dipilih.

## Langkah berikutnya

1. Pengguna memberikan nama penyedia/domain terpilih serta identitas sekolah: nama resmi, NPSN, alamat, email, telepon, tagline, deskripsi, visi, misi, dan logo bila tersedia.
2. Masukkan identitas ke profil lokal, tinjau halaman publik, lalu buat layanan hosting/domain sesuai kepemilikan akun sekolah.
3. Siapkan server dengan `.env.production.example`, jalankan deployment, migrasi, dan `school:release-check`.
4. Sekolah membuat akun nyata dan mengisi data melalui jalur terbatas; uji akun sebelum menonaktifkan demo.
5. Jalankan semua skenario `UAT_REL_001.md`, perbaiki temuan, lalu minta persetujuan admin, guru, dan petugas operasional.
6. Buktikan cron harian dan salinan backup di luar server, lalu tandai Tahap 7 selesai.

## Keputusan dan integrasi

Tahap 7 tetap tidak dicentang. Branding AksiKita sudah terintegrasi; pemulihan dan kesiapan lokal juga terbukti. Keputusan hosting/domain dan penerimaan sekolah tidak boleh disimpulkan dari pengujian lokal. Tidak ada rahasia atau data murid yang ditambahkan ke Git. Perubahan lokal pengguna pada `instruction/sekolah/KEBUTUHAN_DATA_DAN_INFORMASI_SEKOLAH.md` tidak disentuh dan tidak disertakan dalam checkpoint branding.
