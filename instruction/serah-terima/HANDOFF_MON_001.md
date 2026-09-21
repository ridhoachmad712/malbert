# Serah terima MON-001

- Tanggal/waktu/zona: 2026-09-21, Asia/Makassar
- Agen/sesi: Codex lokal
- Status: DONE
- Tujuan dan kriteria penerimaan: menyediakan monitoring per murid, kelas, dan periode; membedakan data belum dinilai; menghitung capaian dari aturan eksplisit; membatasi akses guru; serta menyediakan laporan bulanan cetak dan CSV yang dapat ditelusuri ke sumber.
- Cabang, revisi dasar, revisi kode yang diuji: `main`, dasar `f254c2c`, working tree final MON-001.
- Commit hasil atau cara menemukannya: commit yang memuat berkas serah terima ini; lihat `git log --oneline`.
- Pemilik selanjutnya: tidak ada; PUB-001 siap diambil.

## Hasil dan berkas berubah

- Empat aturan monitoring ditambahkan pada `assessment_settings`: `monitoring_period`, `student_score_method`, `achievement_population`, dan `unassessed_rule`. Kolom nullable menjaga pemulihan backup lama; monitoring tidak menghitung capaian sebelum semuanya diisi.
- `MonitoringReport` menghitung populasi dari riwayat kelas atau peserta berkegiatan, mengambil nilai dari tanggal jurnal sumber, memasukkan koreksi poin ke bulan transaksi asal, dan menghasilkan penyebut serta persentase capaian yang dapat ditelusuri.
- Dasbor kelas menampilkan populasi, data belum dinilai, rata-rata nilai, poin, hasil disetujui, persentase capaian, dan rincian setiap murid. Halaman murid menampilkan perkembangan bulanan sepanjang tahun ajaran.
- Laporan bulanan memiliki filter rombel/bulan, gaya cetak A4 lanskap, dan unduhan CSV. Nama yang diawali karakter formula dinetralkan sebelum ditulis ke CSV.
- Guru hanya dapat membuka rombel yang ditugaskan. Data tidak tersedia dan konfigurasi belum lengkap memiliki keadaan kosong atau pesan tindakan yang jelas.
- Backup naik ke versi 5 dan tetap menerima versi 1 sampai 4. Migrasi lokal hanya menambah empat kolom; tidak ada tabel atau data yang dihapus.
- Berkas utama: `app/Services/MonitoringReport.php`, `app/Http/Controllers/MonitoringController.php`, `resources/views/manage/monitoring`, dan `tests/Feature/MonitoringWorkflowTest.php`.

## Bukti pemeriksaan

| Perintah/interaksi | Waktu | Hasil | Batas pemeriksaan |
| --- | --- | --- | --- |
| `vendor/bin/pint --dirty --format agent` | 2026-09-21 | Lulus | PHP yang berubah |
| `php artisan test --compact tests/Feature/MonitoringWorkflowTest.php tests/Feature/EvaluationWorkflowTest.php` | 2026-09-21 | 16 tes / 99 assertions lulus | Database SQLite uji |
| `vendor/bin/phpunit --testdox` | 2026-09-21 | 73 tes / 497 assertions lulus | Regresi lokal; Drive memakai respons tiruan |
| `node --check public/js/app.js` | 2026-09-21 | Lulus | Sintaks JavaScript |
| `php artisan route:list --except-vendor` | 2026-09-21 | 65 rute dimuat | Registrasi rute |
| `git diff --check` | 2026-09-21 | Lulus | Peringatan normalisasi CRLF CSS tidak mengubah hasil |
| QA browser SQLite terisolasi | 2026-09-21 | Dasbor, murid, laporan, keadaan kosong, filter, CSV, dan kontrol cetak diperiksa; tidak ada error konsol | Data contoh, bukan data sekolah nyata |
| Viewport 668 × 840 dan 1280 × 900 | 2026-09-21 | Lebar halaman 653 dan 1265, lebih kecil dari viewport; tabel memakai area gulir internal | Chrome in-app lokal |

## Delivery Gate UI

Design Read: ruang kerja monitoring untuk guru/admin, memakai bahasa visual pengelolaan yang sudah ada, ENERGY 1 / RHYTHM 1 / MOTION 1. Biru yang sudah ada dipakai sebagai aksen tindakan dan capaian; alur linear filter, periode, ringkasan, lalu tabel mengikuti urutan kerja; tipografi dan jarak lama dipertahankan untuk konsistensi; kartu hanya dipakai untuk statistik yang benar-benar paralel; tidak ada ilustrasi, ikon, atau gerakan baru.

- R-02 PASS: pencarian pada tampilan baru tidak menemukan em dash.
- R-03 PASS: pengukuran 668 px dan 1280 px menunjukkan tidak ada overflow halaman; tabel panjang berada dalam kontainer berlabel yang dapat digulir.
- R-17 PASS: seluruh angka berasal dari query penilaian, jurnal, riwayat kelas, dan transaksi poin; data QA ditandai contoh.
- R-18 PASS: tidak ada testimoni.
- R-23 PASS: tidak ada aset visual atau identitas baru.
- R-24 PASS: semua tautan menuju rute Laravel yang terdaftar.
- R-25 PASS: teks baru memakai warna netral dan status dari palet pengelolaan yang sudah terbaca pada latar terang.
- R-26 PASS: filter, tautan rincian, unduhan CSV, dan kontrol cetak memiliki perilaku nyata; filter, unduhan, dan cetak diklik saat QA.
- R-27 PASS: konfigurasi belum lengkap, populasi kosong, data bulanan kosong, validasi gagal, dan data belum dinilai memiliki keadaan terpisah; tidak ada proses asinkron yang memerlukan loading state.
- R-28 PASS: tidak ada FAQ.
- R-32 PASS: kontrol memakai elemen native, tabel dapat difokuskan dengan keyboard, dan gaya fokus proyek tetap berlaku.
- R-33 PASS: perubahan ditulis langsung pada Blade dan CSS sumber.
- R-34 PASS: tidak ada toggle tema baru.
- R-35 PASS: aplikasi dijalankan; filter rombel, rincian murid, unduhan CSV, dan cetak diperiksa pada browser lokal.
- R-36 PASS: tidak ada klaim keamanan, performa, atau kepatuhan.
- R-37 PASS: arah dan tiga dial dinyatakan sebelum pembuatan UI.
- R-38 PASS: konten realistis hanya berasal dari seeder yang ditandai contoh.
- R-01 PASS: tidak ada gradient atau glow baru.
- R-04 PASS: tidak ada ikon baru.
- R-06 PASS: tipografi lama dipertahankan agar ruang kerja tetap konsisten dan mudah dibaca.
- R-07 PASS: tidak ada pola latar dekoratif.
- R-08 PASS: tombol baru tidak memakai panah dekoratif.
- R-09 PASS: label status hanya menunjukkan keadaan nyata nilai murid.
- R-10 PASS: tidak ada glassmorphism.
- R-12 PASS: hierarki memakai border dan jarak; tidak ada shadow baru pada setiap elemen.
- R-13 PASS: tidak ada glow.
- R-14 PASS: kartu ringkasan seragam karena empat metrik setara; capaian diberi garis aksen untuk prioritas program.
- R-19 PASS: MOTION 1 diterapkan tanpa animasi baru.
- R-22 PASS: tidak ada ilustrasi baru.
- Liveliness PASS: ENERGY 1 / RHYTHM 1 / MOTION 1 konsisten; judul dan capaian menjadi fokus, jarak memisahkan tahap baca, biru menjadi satu aksen, dan motif tabel data proyek dipertahankan.
- C-1 PASS: setiap pilihan visual memiliki alasan pada Design Read.
- C-2 PASS: seluruh kontrol baru berfungsi.
- C-3 PASS: setiap bagian mewakili filter, definisi, angka sumber, rincian, atau keluaran laporan.
- C-4 PASS: keadaan data, dua breakpoint, fokus tabel, ekspor, dan cetak diperiksa tanpa kerusakan halaman.
- C-5 PASS: tidak ada testimoni, statistik, atau capaian yang dibuat-buat.
- R-05 PASS: komposisi mengikuti alur kerja monitoring dan RHYTHM 1, bukan template pemasaran.
- R-11 PASS: radius mengikuti sistem lama dan status memakai radius kecil, bukan semua elemen berbentuk pil.
- R-15 PASS: tindakan memakai label spesifik seperti “Tampilkan periode”, “Unduh CSV”, dan “Cetak laporan”.
- R-16 PASS: tidak ada buzzword pemasaran.
- R-20 PASS: identitas tetap berasal dari bahasa dan motif data TEBAK AKSI.
- R-21 PASS: tema terang yang sudah dipakai ruang pengelolaan dipertahankan; tidak ada perubahan tema.
- R-29 PASS: warna aktif tetap biru, hijau status, dan kuning perhatian ditambah warna netral.
- R-30 PASS: tidak meniru produk lain.
- R-31 PASS: alasan warna, layout, tipografi, jarak, kartu, aset, dan motion dicatat pada Design Read.

## Kondisi belum selesai

- Sekolah tetap perlu memilih nilai konfigurasi nyata. Data demo memakai periode bulanan, rata-rata nilai, murid terdaftar, dan murid belum dinilai sebagai belum berhasil hanya untuk QA.
- Cetak memakai dialog cetak browser agar dapat disimpan sebagai PDF; aplikasi belum membuat berkas PDF server-side.
- Uji bersama guru dan kepala sekolah, data sebenarnya, serta validasi format laporan operasional belum dilakukan.
- Database QA `storage/app/private/mon-qa-0948.sqlite` hanya berisi data contoh dan diabaikan Git. Server serta tab QA telah ditutup.
- Remote Git belum dikonfigurasi, sehingga checkpoint hanya tersedia lokal.

## Langkah berikutnya

1. Klaim PUB-001 dan hubungkan karya internal yang telah disetujui dengan alur seleksi publikasi tanpa membuka catatan internal.
2. Uji izin publikasi, revisi setelah terbit, hubungan sumber, tampilan publik, media, dan regresi aplikasi.
3. Kumpulkan pilihan konfigurasi nyata serta contoh laporan sekolah sebelum uji operasional.

## Keputusan dan integrasi

D-012 mencatat kontrak capaian dan pemisahan laporan bulanan dari klaim target periode. Perubahan terintegrasi pada cabang `main` melalui checkpoint lokal; belum ada remote untuk push.
