# Serah terima FND-001

- Tanggal: 2026-09-21, Asia/Makassar.
- Pelaksana: Codex pemeriksaan lanjutan; tidak ada pekerja paralel atau pemilik aktif setelah checkpoint.
- Cabang/revisi: main, dasar f1b38d9; hasil final adalah commit yang memuat catatan ini. Tidak ada push atau remote.
- Status: DONE setelah implementasi, pengujian otomatis, dan QA browser admin/guru memenuhi kriteria penerimaan tahap 1. Ini bukan status siap rilis seluruh aplikasi.
- Dasar kelanjutan: pengguna meminta melanjutkan setelah analisis proyek. Tugas sebelumnya berstatus systemError, bukan aktif, saat diperiksa.

## Implementasi yang tersedia

AcademicYear, Classroom, Student, Enrollment, relasi penugasan guru, controller/request/service fondasi, halaman pengelolaan, impor CSV, factory/seeder, migrasi, dan pencadangan versi 2. Impor maksimal 500 baris/2 MB, mempertahankan nol depan identitas dan membatalkan seluruh transaksi saat validasi gagal. Guru hanya mengelola murid di kelas yang ditugaskan saat ini. Admin dapat memverifikasi, menonaktifkan dan memindahkan murid tanpa menghapus riwayat.

Filter tahun/rombel pada daftar murid berlabel kelas saat ini. Perilaku ini disengaja untuk daftar operasional, bukan fitur rekap historis. Riwayat seluruh kelas tersedia pada detail bagi admin; guru melihat riwayat kelas yang ditugaskan dalam batas akses murid saat ini.

Semester disimpan sebagai pilihan berjalan pada tahun ajaran. Ini belum merupakan model periode kegiatan. Saat LIT-001 dibuat, simpan identitas periode pada setiap catatan kegiatan agar perubahan semester berjalan tidak mengubah laporan lama. Tidak ada keputusan bisnis sekolah baru yang ditetapkan pada pemeriksaan ini.

## Perubahan sesi pemeriksaan

- tests/Feature/FoundationTest.php: menambah dua regresi untuk perpindahan lintas tahun/filter saat ini/riwayat admin dan pergantian semester yang tidak mengubah keanggotaan.
- public/css/app.css: menambahkan position:relative pada .data-scroll. Elemen .sr-only absolut di tautan tabel sebelumnya keluar dari area gulir, menyebabkan halaman melebar pada ponsel. Teks aksesibilitas tetap dipertahankan.
- TASKS.md, PROJECT_STATUS.md dan serah terima ini: menyelaraskan status dengan kode dan bukti.
- Tidak ada migrasi, seed, atau perubahan data murid yang dilakukan sesi ini. Browser menggunakan data contoh dan sesi admin lokal yang sudah tersedia; pengiriman tahun ajaran tidak valid ditolak sebagaimana diharapkan.

## Finalisasi 2026-09-21

- QA memakai SQLite terpisah `storage/app/private/fnd-qa-final.sqlite` dan server lokal port 8001. Database aplikasi utama tidak diubah.
- Admin berhasil menambah murid dengan NIS bernol depan, memverifikasi, mengubah identitas/status, serta memindahkan murid. Riwayat lama dan baru tampil berurutan.
- Admin berhasil menambah tahun ajaran dan rombel V A dengan guru pendamping. Menyimpan semester pada tahun aktif awalnya menghilangkan penanda periode berjalan. Penyebabnya adalah instance AcademicYear masih memegang `is_current=true` setelah pembaruan massal mengosongkan penanda. Controller kini memuat ulang model sebelum menetapkan tahun aktif.
- Tes regresi semester diperluas untuk ganjil dan genap, memastikan tepat satu tahun tetap aktif dan data murid/keanggotaan tidak berubah.
- Impor CSV dengan satu baris salah menampilkan kesalahan baris dan tidak menyimpan baris valid. Berkas valid dua baris berhasil, mempertahankan NIS/NISN bernol depan dan status belum diverifikasi.
- Guru hanya melihat rombel I A dan V A yang ditugaskan, dapat membuka murid V A, mendapat 404 untuk murid I B, dan 403 untuk pengaturan fondasi admin.

## Bukti verifikasi

- vendor/bin/phpunit tests/Feature/FoundationTest.php --testdox: 26 tes / 174 assertions lulus. Tes baru sempat gagal karena snapshot atribut factory belum dimuat ulang (urutan/default database), lalu diperbaiki dengan snapshot fresh sebelum aksi. Finalisasi kemudian menemukan bug periode berjalan melalui QA browser; regresi dibuat gagal terlebih dahulu dan lulus setelah perbaikan controller.
- vendor/bin/pint --dirty --format agent: lulus.
- vendor/bin/phpunit --testdox: 48 tes / 331 assertions lulus, termasuk pencadangan/pemulihan fondasi. Database tes SQLite in-memory. `php artisan test --compact` dua kali tidak menghasilkan keluaran hingga dihentikan pada lingkungan sesi finalisasi, sehingga hasil final dibuktikan langsung dengan runner PHPUnit yang sama dan konfigurasi phpunit.xml proyek.
- node --check public/js/app.js: lulus.
- git diff --check: lulus; Git hanya memberi pemberitahuan normalisasi CRLF pada CSS/JS.
- Browser localhost: pencarian tanpa hasil menampilkan 0 murid; Hapus filter mengembalikan 3 murid contoh; tautan detail menampilkan identitas/riwayat; panel perpindahan dan impor dapat dibuka; navigasi kelas dan Tambah rombel berjalan.
- Browser: tahun 2026/2028 ditolak, pesan kesalahan tampil, input tetap tersimpan. Tidak ada data tahun ajaran baru yang disimpan.
- Ponsel 390 × 844: sebelum perbaikan, scrollWidth halaman 524; sesudah perbaikan, 375 (area konten mengecualikan scrollbar). Tabel 604 piksel tetap berada dalam area gulir 304 piksel. ArrowRight menggerakkan scroll tabel dan outline fokus terlihat.
- Desktop 1280 × 900: scrollWidth halaman 1265; tabel dan area gulir sama-sama 901 piksel. Tidak ada overflow halaman. Viewport override dikembalikan setelah pemeriksaan.
- Log browser yang tersedia tidak mencatat galat JavaScript.
- Google Drive hanya diuji dengan respons tiruan; tidak membuktikan koneksi nyata.

## Batas QA antarmuka

Perubahan visual terbatas pada batas area gulir tabel, tanpa mengganti warna, tipografi, layout atau gerakan. Tujuan halaman: guru/admin menemukan murid dan membuka data, dengan tabel yang tetap dapat digunakan pada ponsel. ENERGY 1 / RHYTHM 1 / MOTION 1 untuk area pengelolaan; perbaikan tidak menambah gerakan.

- Hard Gate: PASS untuk overflow pada dua ukuran di atas dan gulir keyboard/fokus tabel; audit seluruh kontrol, kontras, dan semua breakpoint belum diverifikasi.
- Purpose-Gate: PASS untuk perubahan ini; pembatas posisi menjaga teks aksesibilitas di area gulir, bukan dekorasi.
- Liveliness: tidak ada desain baru; hierarki dan karakter antarmuka lama dipertahankan, bukan klaim audit seluruh UI.
- Craftsmanship/Quality Locks: PASS untuk lingkup fondasi. Formulir utama admin/guru, validasi, impor, navigasi, akses, dan breakpoint yang dicatat telah diuji; audit UI lama di luar modul fondasi masih belum dilakukan.

## Berkas lokal dan langkah berikutnya

Checkpoint mencakup model/controller/request/service fondasi; empat factory dan empat seeder fondasi; migrasi 2026_09_20_171112; empat view manage (foundation/classroom/student/students); FoundationTest; SchoolBackup; DatabaseSeeder; lang/id/validation.php; public/css/app.css; public/js/app.js; layouts/app.blade.php; routes/web.php; serta konteks proyek.

Berikutnya: mulai LIT-001 dengan kontrak data kegiatan literasi dan periode historis. Jangan menjalankan migrate:fresh pada database lokal. Panduan testing-best-practices tidak ditemukan di lokasi skill proyek/pengguna/plugin yang diperiksa; tes mengikuti konvensi PHPUnit proyek yang tersedia.
