# Serah terima PUB-001

- Tanggal/waktu/zona: 2026-09-21 15:55, Asia/Makassar.
- Agen/sesi: Codex lokal; tanpa pekerja paralel.
- Status: DONE untuk inti integrasi bank karya. Tahap 6 keseluruhan belum selesai karena album folder Drive nyata tetap DRV-001.
- Tujuan dan kriteria penerimaan: memilih hasil yang disetujui, membuat publikasi yang terhubung sumber, memeriksa izin, mencegah data internal bocor, mempertahankan versi terbit saat revisi, serta mengamankan karya teks/tautan/foto/PDF.
- Cabang, revisi dasar, revisi kode yang diuji: `main`, dasar `2a5fb34`; hasil final adalah commit yang memuat catatan ini.
- Commit hasil atau cara menemukannya: `git log -1 -- instruction/serah-terima/HANDOFF_PUB_001.md`.
- Pemilik selanjutnya: tidak ada; periksa TASKS.md sebelum mengambil KAS-001, DRV-001, atau OPS-001.

## Hasil dan berkas berubah

Halaman bank karya menampilkan hasil literasi yang sudah disetujui sesuai akses rombel guru. Setiap hasil hanya dapat memiliki satu publikasi. Pembuatan draf menyimpan hubungan ke sumber, nomor revisi, dan snapshot terbatas berisi identitas internal yang diperlukan untuk pemeriksaan editor. Konten publik hanya menerima karya terpilih serta kredit yang disetujui; jurnal, refleksi, umpan balik, nilai, NIS, dan NISN tidak disalin.

Karya teks dan tautan disimpan dalam snapshot draf. Foto dan PDF disalin ke `publication-assets` sehingga rute lampiran privat sumber tidak dibuka. Aset draf hanya dapat dibaca penulis/admin; aset menjadi publik hanya bila berada pada `published_content`. Kredit kelas adalah pilihan awal. Menampilkan nama murid memerlukan konfirmasi izin terpisah, selain pemeriksaan izin publikasi umum dan pemeriksaan admin.

Mekanisme publikasi yang ada tetap dipakai: revisi mengubah `content`, sedangkan pengunjung membaca `published_content` terakhir sampai admin menerbitkan ulang. Migrasi menambah `literacy_submission_id`, `source_revision_number`, dan `source_snapshot` ke publikasi. Backup versi 6 mencakup `publication-assets` dan tetap menerima versi 1 sampai 5.

## Bukti pemeriksaan

| Perintah/interaksi | Waktu | Hasil | Batas pemeriksaan |
| --- | --- | --- | --- |
| `vendor/bin/pint --dirty --format agent` | 2026-09-21 | Lulus | PHP yang berubah |
| `php artisan test --compact tests/Feature/PublicationWorkflowTest.php` | 2026-09-21 | 4 tes / 44 assertions lulus | SQLite in-memory; seleksi, privasi, izin identitas, versi terbit, aset, akses, dan backup |
| `php artisan test --compact` | 2026-09-21 | 77 tes / 541 assertions lulus | Regresi seluruh aplikasi |
| `php artisan route:list --except-vendor` | 2026-09-21 | 68 rute berhasil dimuat | Bootstrap controller dan rute |
| `php artisan migrate --no-interaction` setelah `config:show` dan `migrate:status` | 2026-09-21 | Migrasi aditif berhasil pada MySQL lokal | Tidak memakai `migrate:fresh`; data lokal tidak dihapus |
| Browser lokal `/kelola/bank-karya` | 2026-09-21 | Desktop dan 390 × 844 tampil tanpa overflow yang terlihat; filter, pesan privasi, keadaan kosong, dan navigasi terbaca; tidak ada error/warning konsol | Database lokal tidak memiliki karya disetujui; keadaan berisi dan editor sumber dibuktikan lewat tes render |
| `git diff --check` | 2026-09-21 | Lulus | Pemeriksaan whitespace sebelum checkpoint |

## Kondisi belum selesai

Album folder Google Drive nyata belum diuji karena API key dan akses Google Cloud belum tersedia. Hal itu tetap dicatat sebagai DRV-001 dan membuat kotak Tahap 6 pada rancangan belum dapat dicentang. Capaian program sudah tersedia dari MON-001; tautan berbagi khusus di luar URL cerita publik tidak ditambahkan karena belum ada kebutuhan atau aturan baru.

Uji bersama sekolah, audit aksesibilitas menyeluruh, data karya nyata, deployment, dan pemulihan cadangan operasional berada di luar PUB-001. Snapshot sumber menyimpan nama murid secara internal agar editor dapat memeriksa kredit, tetapi nama tidak masuk halaman publik kecuali opsi nama dipilih dan izin dikonfirmasi.

## Langkah berikutnya

Untuk DRV-001, sediakan akses Google Cloud dan API key, lalu uji folder nyata tanpa login Google serta perbarui bukti tahap 6. Untuk KAS-001, sekolah perlu menetapkan matriks akses, alur penanganan, pengaduan anonim, waktu respons, dan retensi lampiran. Untuk operasi lintas perangkat, pilih remote privat pada OPS-001.

## Keputusan dan integrasi

Implementasi mengikuti D-003, D-006, D-010, dan D-013. Perubahan berada langsung pada `main` dan dicatat dalam commit checkpoint lokal. Remote masih belum dikonfigurasi, sehingga tidak ada push. Tidak ada password, token, data murid nyata, atau tautan sesi autentikasi dalam checkpoint.
