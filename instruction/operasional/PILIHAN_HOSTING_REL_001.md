# Pilihan hosting dan domain REL-001

Dokumen ini mencatat perbandingan pada 22 September 2026. Harga promosi, harga perpanjangan, dan fitur paket dapat berubah; periksa kembali halaman resmi sebelum membayar. Belum ada layanan yang dibeli atau akun penyedia yang dibuat.

## Kebutuhan yang tidak boleh dikurangi

- PHP 8.3 untuk web dan CLI, Composer, serta ekstensi yang lulus `composer check-platform-reqs`.
- MySQL, SSH/terminal, HTTPS, dan kemampuan mengarahkan document root ke folder `public`.
- Cron setiap menit untuk `php artisan schedule:run`.
- Penyimpanan privat yang persisten untuk karya, logo, dan cadangan aplikasi.
- Koneksi HTTPS keluar ke Google Drive API.
- Salinan cadangan di luar akun/server hosting dan jalur pemulihan yang dapat diuji.

## Perbandingan

| Opsi | Biaya dan kapasitas yang ditemukan | Kesesuaian | Catatan keputusan |
| --- | --- | --- | --- |
| **Hostinger Unlimited** | Promosi Rp38.900/bulan; perpanjangan Rp121.900/bulan. 2 core, RAM 3 GB, NVMe 50 GB, dan backup harian. | Paling sesuai untuk peluncuran awal yang dikelola sekolah: SSH, Git deployment, database, dan cron tersedia pada hosting web. | **Rekomendasi utama**, dengan syarat staging membuktikan Laravel 13, PHP 8.3 CLI, cron setiap menit, document root `public`, dan restore. Harga promosi memerlukan pembayaran jangka panjang di muka. |
| **Hostinger Premium** | Promosi Rp24.900/bulan untuk 48 bulan (Rp1.195.200 di muka); perpanjangan Rp84.900/bulan. 1 core, RAM 2 GB, SSD 20 GB, backup mingguan. | Cukup untuk pilot kecil dan paling hemat dari opsi terkelola yang diperiksa. | Cadangan penyedia hanya mingguan dan ruang tumbuh lebih kecil. Tetap wajib menyalin backup aplikasi keluar server. |
| **Laravel Cloud Starter** | Biaya platform $0 ditambah pemakaian compute, database, dan penyimpanan; domain, HTTPS, serta scheduler dikelola. | Operasional Laravel paling sederhana dan region Singapore tersedia. | Aplikasi saat ini memakai disk lokal privat. Sebelum opsi ini dipakai, media dan backup harus dipindahkan ke object storage serta biaya bulanan perlu dipantau dalam USD. |
| **IDCloudHost Cloud/VPS** | Cloud VPS ditawarkan mulai sekitar Rp50.000/bulan; paket VPS yang kapasitasnya memadai bergantung ketersediaan. | Lokasi Indonesia tersedia dan kontrol server penuh. | Memerlukan pengelolaan OS, firewall, patch, PHP, database, TLS, monitoring, dan backup. Pilih hanya jika ada petugas server yang bertanggung jawab. |
| **Rumahweb shared hosting** | Medium Rp29.900/bulan (1,5 core/RAM 2 GB); Large Rp49.900/bulan (2 core/RAM 3 GB). | SSH dan backup mingguan tersedia. | Interval cron yang dipublikasikan adalah 15 menit untuk Medium dan 5 menit untuk Large, sehingga tidak memenuhi scheduler Laravel setiap menit. Bukan pilihan utama REL-001. |

Sumber resmi: [harga Hostinger](https://www.hostinger.com/id/harga), [batas paket Hostinger](https://www.hostinger.com/support/6976044-parameters-and-limits-of-hosting-plans-in-hostinger/), [cron Hostinger](https://www.hostinger.com/support/1583765-how-many-cron-jobs-can-you-set-up-in-hostinger/), [Git deployment Hostinger](https://www.hostinger.com/support/1583302-how-to-deploy-a-git-repository-in-hostinger/), [dukungan framework Hostinger](https://www.hostinger.com/support/which-programming-languages-and-frameworks-are-supported-at-hostinger/), [harga Laravel Cloud](https://marketing.cloud.laravel.com/pricing), [compute Laravel Cloud](https://marketing.cloud.laravel.com/compute), [IDCloudHost Cloud VPS](https://idcloudhost.com/cloud-vps/), [Rumahweb hosting](https://www.rumahweb.com/hosting-murah/), dan [cron Rumahweb](https://www.rumahweb.com/journal/cron-job-adalah/).

## Rekomendasi

Gunakan **Hostinger Unlimited** untuk rilis sekolah pertama apabila sekolah belum memiliki hosting yang memenuhi kebutuhan di atas. Paket ini memberi ruang yang lebih aman untuk PHP, database, media lokal, dan backup harian tanpa beban mengelola VPS. Hostinger saat ini hanya menyebut auto-installer Laravel sampai versi 12.x, sedangkan aplikasi memakai Laravel 13. Karena itu deployment harus memakai Composer/Git secara manual dan baru diterima setelah staging lulus; keberadaan label dukungan Laravel saja belum cukup.

Jika anggaran awal harus ditekan, Hostinger Premium dapat dipakai untuk pilot dengan jumlah pengguna kecil. Perbandingan biaya harus memakai harga perpanjangan, bukan hanya angka promosi.

## Domain

Jika sekolah sudah memiliki domain `.sch.id`, gunakan subdomain seperti `tebakaksi.<domain-sekolah>.sch.id`. Subdomain tidak memerlukan pembelian domain kedua. Jika belum ada, daftarkan `<nama-sekolah>.sch.id` atas nama sekolah, kemudian pasang aplikasi pada subdomain `tebakaksi`.

Pendaftaran `.sch.id` diperuntukkan bagi sekolah dasar dan menengah serta memerlukan dokumen kelembagaan. Siapkan sekurang-kurangnya surat keterangan/keputusan pendirian, identitas kepala sekolah, dan surat permohonan resmi sesuai pemeriksaan registrar serta [kebijakan PANDI](https://pandi.id/public/files/2025/8/kebijakan-pendaftaran-nama-domain-versi-10-0-bilingual-new-1756441612.pdf). Contoh daftar persyaratan dan harga tersedia pada [panduan domain Indonesia Hostinger](https://www.hostinger.com/id/tutorial/kelebihan-domain-id/).

## Pemeriksaan sebelum pembayaran

Minta penyedia mengonfirmasi secara tertulis:

1. PHP 8.3 tersedia untuk web dan CLI, Composer dapat dijalankan melalui SSH, dan proses Artisan tidak diblokir.
2. Cron `* * * * *` diizinkan, bukan hanya interval 5 atau 15 menit.
3. Document root domain/subdomain dapat diarahkan ke `<aplikasi>/public` tanpa memindahkan isi aplikasi ke direktori publik.
4. `storage` bersifat persisten, symlink Laravel diizinkan, dan batas file/ruang cukup untuk media sekolah.
5. Koneksi HTTPS keluar ke Google Drive API diizinkan.
6. MySQL, backup penyedia, restore mandiri/dukungan restore, SSL, dan SSH termasuk dalam paket.

Setelah enam jawaban diterima, buat staging, ikuti [panduan peluncuran](PANDUAN_PELUNCURAN.md), dan jalankan seluruh [UAT REL-001](UAT_REL_001.md). Jangan memasukkan data murid nyata sebelum HTTPS, kontrol akses, backup, dan pemulihan staging terbukti.
