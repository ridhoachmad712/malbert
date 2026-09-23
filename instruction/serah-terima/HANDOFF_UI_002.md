# Serah terima UI-002

- Tanggal/waktu/zona: 2026-09-23 Asia/Makassar
- Agen/sesi: Codex lokal
- Status: DONE
- Tujuan dan kriteria penerimaan: admin dapat mengubah warna, teks utama, foto publik, dan identitas aplikasi tanpa mengedit kode; perubahan memiliki pratinjau, validasi, kontrol akses, tampilan ponsel, reset bawaan, dan bukti regresi.
- Cabang dan revisi dasar: `main`, dasar `9c3c64b`, commit hasil dapat ditemukan dengan `git log -1 -- instruction/serah-terima/HANDOFF_UI_002.md`.
- Pemilik selanjutnya: tidak ada.

## Hasil

- Menu admin **Tampilan & Konten** tersedia pada `/kelola/tampilan` dan hanya dapat digunakan admin.
- Pengaturan mencakup nama aplikasi, nama penjelas, warna utama/judul/teks/teks sekunder/aksen/latar, teks halaman utama, teks halaman masuk, serta tagline footer.
- Admin dapat mengganti gambar halaman utama, halaman masuk, profil sekolah, dan favicon. Gambar dikompresi ke WebP, disimpan privat, lalu hanya disajikan melalui slot aset publik yang dibatasi.
- Pratinjau memperbarui teks, palet, dan gambar utama sebelum penyimpanan. Tombol reset mengembalikan tema AksiKita tanpa mengubah identitas sekolah, publikasi, akun, atau data siswa.
- Warna utama diperiksa terhadap teks putih; warna judul, isi, sekunder, dan aksen hijau diperiksa terhadap latar dengan rasio minimum 4,5:1.
- Penyimpanan Profil sekolah memakai penggabungan data sehingga tema tidak terhapus ketika identitas sekolah diperbarui.
- Data tema berada dalam JSON `school_settings`; berkas gambar memakai folder `media` yang sudah tercakup perintah backup/restore.
- Editor HTML/CSS bebas tidak ditambahkan agar tampilan responsif, escaping, dan pembatasan konten tetap terjaga.

## Bukti pemeriksaan

| Pemeriksaan | Hasil | Cakupan |
| --- | --- | --- |
| `php artisan test --compact` | PASS, 85 tes / 597 assertions | Akses admin/guru/tamu, simpan tema, media, aset publik, kontras, reset, preservasi profil, dan regresi aplikasi |
| `vendor/bin/pint --dirty --format agent` | PASS | Seluruh PHP yang berubah |
| `node --check public/js/app.js` | PASS | Sintaks pratinjau langsung dan JavaScript lama |
| `php artisan view:cache` | PASS | Kompilasi seluruh Blade |
| `php artisan route:list --except-vendor` | PASS | Rute edit, simpan, reset, dan aset tampilan terdaftar |
| Browser desktop | PASS | Struktur form, panel pratinjau, sidebar, dan hierarki konten |
| Browser 390 × 844 | PASS | Form satu kolom, navigasi, input, dan pratinjau tanpa overflow |
| Interaksi pratinjau nama aplikasi | PASS | Nilai pratinjau berubah langsung dan tidak tersimpan sebelum submit |

## Batas dan langkah berikutnya

- Pengaturan menyediakan konten merek dan permukaan publik utama. Label operasional, validasi, istilah penilaian, dan isi publikasi tetap dikelola oleh kode atau modulnya sendiri agar alur kerja tidak rusak.
- Font dan struktur tata letak tidak dibuka sebagai pengaturan bebas. Penambahan pilihan font/layout sebaiknya berupa preset yang diuji bila kelak dibutuhkan.
- REL-001 tetap menunggu nilai identitas sekolah, rincian dan pembuatan hosting/domain, staging, akun nyata, serta UAT sekolah.
- Perubahan lokal pengguna pada `instruction/sekolah/KEBUTUHAN_DATA_DAN_INFORMASI_SEKOLAH.md` dipertahankan dan tidak termasuk dalam checkpoint UI-002.
