# Serah terima DRV-001

- Tanggal/waktu/zona: 2026-09-21 16:46, Asia/Makassar.
- Agen/sesi: Codex lokal; tanpa pekerja paralel.
- Status: DONE.
- Tujuan dan kriteria penerimaan: mengaktifkan Google Drive API, mengonfigurasi key dengan aman, mengimpor foto/video dari folder nyata, dan membuktikan album publik dapat dilihat tanpa login Google.
- Cabang dan revisi dasar: `main`, `62a1ecf`.
- Pemilik selanjutnya: tidak ada.

## Hasil

Google Drive API telah diaktifkan pada proyek Cloud yang dipilih pengguna. API key bernama `Ruang Cerita Drive Album` dibuat dengan pembatasan API hanya ke Google Drive API. Nilai key disimpan pada `GOOGLE_DRIVE_API_KEY` di `.env` lokal, tidak dicetak dalam keluaran, dan tidak masuk Git. Laravel sudah membaca konfigurasi tersebut setelah cache konfigurasi dibersihkan.

Folder publik nyata yang diberikan pengguna berhasil diimpor menjadi 8 foto. Publikasi QA sementara merender delapan slide pada halaman publik. Delapan URL pratinjau Drive merespons HTTP 200 melalui permintaan tanpa cookie, media terlihat pada browser, dan tombol berikutnya mengubah posisi slider dari 1/8 menjadi 2/8. Publikasi QA kemudian dihapus sehingga tidak menjadi data aplikasi permanen.

Pembatasan aplikasi key masih `None` karena alamat IP keluar server produksi belum tersedia. Saat hosting ditentukan dan memiliki IP tetap, tambahkan pembatasan alamat IP tanpa menghapus pembatasan Google Drive API.

## Bukti dan batas

| Pemeriksaan | Hasil | Batas |
| --- | --- | --- |
| `php artisan config:clear` dan pemeriksaan `config('services.drive.api_key')` | Konfigurasi terisi | Nilai key tidak dicetak |
| Pemeriksaan Google Cloud Console | Drive API aktif; key dibatasi ke Google Drive API | Pembatasan IP menunggu hosting |
| Impor folder nyata melalui `DriveAlbumService` | 8 foto ditemukan dan URL folder dinormalisasi | Isi folder dapat berubah di luar aplikasi |
| Delapan permintaan pratinjau tanpa cookie | Seluruhnya HTTP 200 | Membuktikan akses saat pengujian |
| Halaman publik QA | HTTP 200, 8 slide/8 embed, foto terlihat, navigasi 1/8 → 2/8 | Publikasi QA dihapus setelah pemeriksaan |
| `php artisan test --compact tests/Feature/DriveAlbumTest.php` | 6 tes / 55 assertions lulus | Respons API pada tes otomatis memakai tiruan |
| `php artisan test --compact` | 77 tes / 541 assertions lulus | Regresi aplikasi dengan SQLite in-memory |

Tes otomatis tetap memakai respons HTTP tiruan agar stabil dan mencakup kegagalan, pagination, batas 200 media, dan akses. Uji folder nyata melengkapi bukti eksternal yang tidak dapat diberikan oleh tes tersebut.

## Langkah berikutnya

Saat hosting memiliki alamat IP keluar tetap, tambahkan pembatasan alamat IP pada key. Untuk penggunaan sekolah, pertahankan akses “Siapa saja yang memiliki link” pada folder dan file album, serta periksa kembali akses media sebelum admin menerbitkan cerita.

Tidak ada rahasia, API key, atau tautan sesi autentikasi dalam catatan ini.
