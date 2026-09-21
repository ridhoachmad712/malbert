# Serah terima NIL-001

- Tanggal/waktu/zona: 2026-09-21, Asia/Makassar
- Agen/sesi: Codex lokal
- Status: DONE
- Tujuan dan kriteria penerimaan: menyediakan rubrik observasi, nilai otomatis, umpan balik, poin aktivitas, tingkatan apresiasi, pencegahan poin ganda, serta koreksi yang dapat ditelusuri; nilai kemampuan tetap terpisah dari poin aktivitas.
- Cabang, revisi dasar, revisi kode yang diuji: `main`, dasar `6113431`, working tree final NIL-001.
- Commit hasil atau cara menemukannya: commit yang memuat berkas serah terima ini; lihat `git log --oneline`.
- Pemilik selanjutnya: tidak ada; MON-001 belum ditugaskan.

## Hasil dan berkas berubah

- Admin memperoleh gerbang konfigurasi wajib untuk pembulatan nilai, ambang keberhasilan per murid, periode poin, dan aturan pengulangan. Nilai demo hanya dipakai pada lingkungan lokal/testing.
- Guru menilai hasil literasi yang telah disetujui memakai enam indikator bernilai 1–4. Sistem menghitung nilai dan kategori, menyimpan umpan balik, periode sumber, versi, serta pembuat/perubah terakhir.
- Perubahan penilaian yang sudah tersimpan memerlukan alasan dan menghasilkan snapshot revisi yang tidak dapat diubah.
- Poin aktivitas memakai transaksi terpisah dengan nilai baku dari dokumen sumber. Kunci sumber mencegah poin ganda; koreksi berupa transaksi pembatalan atau pemulihan dengan alasan dan referensi ke transaksi asal.
- Tingkatan apresiasi dihitung dari total poin pada periode aktif tanpa menggabungkannya dengan nilai kemampuan.
- Halaman pengelolaan, rute bernama, relasi model, factories/seeder, serta backup/pemulihan versi 4 telah ditambahkan. Berkas utama berada pada `app/Http/Controllers/EvaluationController.php`, `resources/views/manage/evaluation`, dan `tests/Feature/EvaluationWorkflowTest.php`.

## Bukti pemeriksaan

| Perintah/interaksi | Waktu | Hasil | Batas pemeriksaan |
| --- | --- | --- | --- |
| `vendor/bin/pint --dirty --format agent` | 2026-09-21 | Lulus | Memformat PHP yang berubah |
| `php artisan test --compact tests/Feature/EvaluationWorkflowTest.php` | 2026-09-21 | 9 tes / 62 assertions lulus | Database uji PHPUnit |
| `vendor/bin/phpunit --testdox` | 2026-09-21 | 66 tes / 460 assertions lulus | Regresi lokal; Drive memakai respons tiruan |
| `node --check public/js/app.js` | 2026-09-21 | Lulus | Pemeriksaan sintaks |
| `php artisan route:list --except-vendor` | 2026-09-21 | 61 rute dimuat | Pemeriksaan registrasi rute |
| `git diff --check` | 2026-09-21 | Lulus | Peringatan normalisasi CRLF pada CSS tidak mengubah hasil |
| Browser lokal dengan data demo terisolasi | 2026-09-21 | Ringkasan, konfigurasi, dan transaksi poin tampil baik pada lebar 668 px; tidak ada overflow halaman pada 1280 × 900 | Data contoh; tidak menggantikan uji pengguna sekolah |

## Kondisi belum selesai

- Sekolah belum memilih nilai konfigurasi nyata. Data demo memakai pembulatan bilangan bulat, ambang 76, periode bulanan, dan batas satu aktivitas per hari hanya untuk QA.
- Definisi capaian program 80% masih memerlukan periode pengukuran, penyebut populasi, dan perlakuan data belum dinilai. MON-001 tidak boleh menyatakan capaian sebelum aturan tersebut dibakukan.
- Uji browser tidak menilai formulir rubrik dengan hasil murid nyata karena data demo terisolasi belum memiliki pengajuan yang disetujui; render, validasi, penyimpanan, akses, dan koreksinya dibuktikan oleh tes fitur.
- Saat pemeriksaan migrasi, `php artisan migrate:fresh --env=testing --no-interaction` ternyata tetap menunjuk MySQL lokal dan mereset database sebelum migrasi gagal akibat nama indeks terlalu panjang. Nama indeks telah diperpendek menjadi `assessment_period_index`, tabel parsial kosong dihapus, dan migrasi dijalankan ulang. Database dipulihkan dari cadangan terbaru yang tersedia `storage/app/private/backups/school-20260919-164003-UtkKg9.zip`: 2 pengguna, 7 publikasi, 1 pengaturan sekolah, tanpa tahun ajaran atau murid, sesuai isi arsip. QA berikutnya memakai `storage/app/private/nil-qa.sqlite` dengan target database eksplisit. Periksa target database aktual sebelum menjalankan perintah destruktif.
- Remote Git belum dikonfigurasi, sehingga checkpoint hanya tersedia secara lokal.

## Langkah berikutnya

1. Tetapkan definisi capaian 80% bersama sekolah: periode, populasi penyebut, dan perlakuan murid yang belum dinilai.
2. Klaim MON-001 di `TASKS.md`, lalu bangun dasbor per murid/kelas/periode dan laporan bulanan dengan angka yang dapat ditelusuri ke penilaian serta transaksi poin.
3. Uji filter akses guru, data belum dinilai, cetak/unduh, perhitungan capaian, responsivitas, dan regresi seluruh aplikasi.

## Keputusan dan integrasi

D-005 tetap memisahkan nilai kemampuan dari poin aktivitas. D-011 mencatat konfigurasi wajib dan model koreksi terlacak. Perubahan terintegrasi pada cabang `main` melalui checkpoint lokal; belum ada remote untuk push.
