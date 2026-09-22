# Panduan peluncuran TEBAK AKSI

Panduan ini berlaku setelah sekolah memilih hosting dan domain. Rekomendasi dan daftar pemeriksaan penyedia dicatat pada [Pilihan hosting dan domain REL-001](PILIHAN_HOSTING_REL_001.md). Jangan menyimpan `.env`, kata sandi, API key, database, atau data murid di Git. Web root hosting wajib mengarah ke folder `public`.

## Kebutuhan server

- PHP 8.3 beserta ekstensi yang lulus `composer check-platform-reqs`, termasuk PDO driver database, `fileinfo`, dan `zip`.
- MySQL/PostgreSQL yang hanya dapat diakses oleh aplikasi dan pengelola server.
- HTTPS aktif pada domain sekolah.
- Izin tulis untuk `storage` dan `bootstrap/cache`.
- Cron yang dapat menjalankan Artisan setiap menit.
- Lokasi cadangan di luar server aplikasi. Cadangan lokal aplikasi disimpan 14 hari dan tidak mencakup file asli yang hanya ditautkan dari Google Drive.

## Persiapan rilis

1. Salin kode dari revisi Git yang sudah diuji.
2. Jalankan `composer install --no-dev --optimize-autoloader --no-interaction`.
3. Salin `.env.production.example` menjadi `.env`, lalu isi domain, database, dan key Drive pada server.
4. Untuk instalasi baru saja, jalankan `php artisan key:generate`. Jangan mengganti `APP_KEY` pada instalasi yang sudah berisi sesi atau data terenkripsi.
5. Jalankan `php artisan migrate --force --no-interaction`.
6. Jalankan `php artisan storage:link` bila hosting memerlukan tautan berkas publik.
7. Isi profil sekolah dan buat akun admin/guru nyata melalui aplikasi. Uji akun tersebut sebelum akun demo dihapus atau dinonaktifkan.
8. Jalankan `php artisan school:release-check`. Semua baris harus berstatus `SIAP`.
9. Setelah konfigurasi final, jalankan `php artisan optimize`.

Jangan menjalankan `db:seed` di produksi. Seeder demo memang dibatasi agar tidak membuat akun/contoh di lingkungan produksi, tetapi data sekolah harus dibuat melalui alur aplikasi dan impor yang ditinjau.

## Scheduler dan cadangan

Tambahkan satu cron pada server:

```text
* * * * * cd /path/aplikasi && php artisan schedule:run >> /dev/null 2>&1
```

Periksa dengan `php artisan schedule:list`; tugas `school:backup` harus terjadwal setiap hari pukul 02.00 zona Asia/Makassar. Jalankan `php artisan school:backup --no-interaction` untuk membuat cadangan manual sebelum migrasi atau perubahan besar. Salin hasil dari `storage/app/private/backups` ke penyimpanan lain yang aksesnya dibatasi.

## Pemulihan

Pemulihan hanya menerima database aplikasi yang kosong. Gunakan database baru, jangan mengosongkan database aktif untuk mencoba restore.

1. Aktifkan maintenance mode pada lingkungan tujuan dengan `php artisan down`.
2. Siapkan database kosong dan jalankan migrasi untuk revisi aplikasi yang sesuai.
3. Jalankan `php artisan school:backup --restore=/path/cadangan.zip --no-interaction`.
4. Cocokkan jumlah data penting dan buka media lokal. File asli Drive tetap berada di Drive dan harus diuji terpisah.
5. Jalankan `php artisan school:release-check` dan UAT singkat untuk login, halaman publik, literasi, penilaian, monitoring, serta publikasi.
6. Jalankan `php artisan up` setelah hasil dinyatakan benar.

Pemulihan REL-001 pada 21 September 2026 berhasil di database dan penyimpanan terisolasi: migrasi serta restore keluar dengan kode 0, dan jumlah pada 17 tabel sama dengan isi cadangan tanpa selisih. Cadangan saat itu tidak memiliki berkas lokal, sehingga jalur media tetap dibuktikan oleh tes otomatis backup yang memakai media tiruan.

## Pemeriksaan setelah domain aktif

- `/up` merespons sukses melalui HTTPS.
- Halaman publik, login, dan area kelola tidak menampilkan error debug.
- Cookie sesi bertanda Secure dan tidak dapat dibaca JavaScript.
- Folder Drive publik dapat dibuka tanpa login Google; key API dibatasi ke Google Drive API dan, bila hosting menyediakan alamat keluar tetap, ke alamat tersebut.
- Cron benar-benar menghasilkan cadangan baru pada hari berikutnya dan salinannya tersedia di luar server.
- Log aplikasi dapat dibaca petugas tanpa membuka akses publik ke `storage`.

## Batas peluncuran

Peluncuran baru dinyatakan selesai setelah [UAT sekolah](UAT_REL_001.md) disetujui, akun demo ditangani, data contoh tidak tampil sebagai data nyata, pemulihan cadangan dibuktikan, dan seluruh pemeriksaan `school:release-check` berstatus `SIAP` pada server tujuan.
