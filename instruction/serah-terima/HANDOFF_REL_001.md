# Serah terima REL-001

- Tanggal/waktu/zona: 2026-09-22 Asia/Makassar
- Agen/sesi: Codex lokal
- Status: BLOCKED
- Tujuan dan kriteria penerimaan: uji bersama guru pada ponsel, perbaikan alur, pemulihan cadangan, hosting/domain, akun sebenarnya, dan aktivasi jadwal cadangan. Selesai hanya jika sekolah telah mencoba alur utama, restore terbukti, konfigurasi produksi siap, serta akun/data contoh ditangani.
- Cabang, revisi dasar, revisi kode yang diuji: `main`, dasar `be1fea4`, working tree rekomendasi hosting sebelum commit.
- Commit hasil atau cara menemukannya: commit checkpoint REL-001 pada `main`; lihat `git log -1 -- instruction/serah-terima/HANDOFF_REL_001.md`.
- Pemilik selanjutnya: belum ditugaskan; lanjutkan setelah pengguna menerima rekomendasi atau menyebut hosting/domain yang sudah dimiliki, lalu sekolah siap UAT.

## Hasil dan berkas berubah

- `school:release-check` memeriksa lingkungan produksi, debug, HTTPS, APP_KEY, keamanan sesi, migrasi, penyimpanan privat, jadwal backup, key Drive, profil sekolah, akun demo, serta ketersediaan admin/guru aktif.
- `.env.production.example` menyediakan nilai aman tanpa rahasia untuk lingkungan produksi.
- `instruction/operasional/PANDUAN_PELUNCURAN.md` memuat kebutuhan server, deployment, cron, backup/restore, pemeriksaan domain, dan batas peluncuran.
- `instruction/operasional/UAT_REL_001.md` memuat 18 skenario penerimaan untuk pengunjung, admin, guru, dan petugas server.
- `phpunit.xml` memakai jalur cache khusus tes. Ini mencegah proses tes membaca cache konfigurasi lokal/produksi dan menyentuh database yang salah.
- `.gitignore` mengizinkan template produksi dilacak tanpa mengizinkan `.env` produksi sebenarnya.
- `instruction/operasional/PILIHAN_HOSTING_REL_001.md` membandingkan pilihan terkelola dan VPS, menetapkan Hostinger Unlimited sebagai rekomendasi bersyarat, menjelaskan pilihan domain `.sch.id`, dan memberi enam pemeriksaan sebelum pembayaran.

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

## Insiden verifikasi dan pemulihan

Tes dan `php artisan optimize` sempat dijalankan bersamaan. Cache konfigurasi lokal yang dibuat pada saat itu terbaca oleh proses tes, sehingga `RefreshDatabase` mengosongkan tabel aplikasi MySQL lokal. Backup yang dibuat tepat sebelum simulasi dipakai untuk memulihkan database. Pemeriksaan setelah restore menunjukkan 17 tabel tanpa selisih: 2 pengguna, 7 publikasi, dan 1 profil sekolah kembali sesuai arsip. Konfigurasi cache PHPUnit kemudian dipisahkan dan dibuktikan dengan menjalankan tes saat cache konfigurasi lokal aktif; data lokal tetap utuh. Jangan menjalankan perintah cache/optimize bersamaan dengan tes.

## Kondisi belum selesai

- Hosting dan domain belum dipilih; deployment nyata, DNS, HTTPS, pembatasan IP key Drive, serta cron server belum dapat diuji.
- Identitas sekolah, akun admin/guru nyata, data kelas/murid, dan konfigurasi aturan sekolah belum diberikan.
- Delapan belas skenario UAT masih `BELUM` karena memerlukan perwakilan sekolah dan perangkat yang benar-benar digunakan.
- Salinan backup di luar server dan bukti cron menghasilkan backup hari berikutnya belum tersedia.
- `school:release-check` lokal masih memiliki tujuh item `BELUM`, sesuai kondisi development.
- Remote Git privat belum tersedia; checkpoint hanya lokal.
- Rekomendasi hosting belum menjadi keputusan/pembelian. Kompatibilitas Laravel 13, PHP CLI, cron setiap menit, document root, dan restore masih harus dibuktikan pada staging penyedia yang dipilih.

## Langkah berikutnya

1. Pengguna menerima rekomendasi Hostinger Unlimited atau menyebut hosting/domain yang sudah dimiliki. Sebelum pembayaran, konfirmasi enam kebutuhan pada `PILIHAN_HOSTING_REL_001.md`.
2. Siapkan server dengan `.env.production.example`, jalankan deployment, migrasi, dan `school:release-check`.
3. Sekolah membuat akun nyata dan mengisi identitas/data melalui jalur terbatas; uji akun sebelum menonaktifkan demo.
4. Jalankan semua skenario `UAT_REL_001.md`, perbaiki temuan, lalu minta persetujuan admin, guru, dan petugas operasional.
5. Buktikan cron harian dan salinan backup di luar server, lalu tandai Tahap 7 selesai.

## Keputusan dan integrasi

Tahap 7 tetap tidak dicentang. Pemulihan dan kesiapan lokal sudah terbukti, sedangkan keputusan hosting/domain dan penerimaan sekolah tidak boleh disimpulkan dari pengujian lokal. Tidak ada rahasia atau data murid yang ditambahkan ke Git.
