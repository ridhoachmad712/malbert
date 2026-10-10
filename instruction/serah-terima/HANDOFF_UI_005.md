# Serah terima UI-005

- Tanggal/waktu/zona: 2026-10-10, Asia/Makassar.
- Agen/sesi: Codex lokal; tanpa pekerja paralel.
- Status: DONE untuk penyelarasan alur pengelolaan dengan rancangan awal.
- Tujuan dan kriteria penerimaan: dasbor mengutamakan pekerjaan belajar guru, admin melihat antrean persetujuan, menu mengikuti urutan alur, daftar publikasi lengkap tetap dapat dicari, dan antarmuka dapat dipakai pada ponsel.
- Cabang dan revisi dasar: `main`, `a3d9672`; revisi kode yang diuji adalah working tree sebelum checkpoint UI-005.
- Commit hasil: commit yang memuat catatan ini; temukan dengan `git log -1 -- instruction/serah-terima/HANDOFF_UI_005.md`.
- Pemilik selanjutnya: tidak ada.

## Hasil dan berkas berubah

`DashboardController` serta halaman dasbor kini menampilkan rombel yang dapat diakses, tugas aktif, hasil yang perlu ditinjau, dan hasil disetujui yang belum dinilai. Guru hanya melihat pekerjaan rombel yang ditugaskan. Jika belum ditugaskan, dasbor menjelaskan prasyarat dan tidak menampilkan tindakan membuat tugas. Admin melihat antrean publikasi, dan keadaan aturan penilaian belum ditetapkan diberi penjelasan.

Menu pengelolaan mendahulukan Pembelajaran sebelum Publikasi. Daftar publikasi lengkap dipindahkan dari dasbor ke `/kelola/konten`, dengan pencarian, filter status, paginasi, batas akses penulis, dan tautan dari menu, dasbor, editor, serta bank karya. Pada ponsel daftar menjadi kartu sehingga status dan tindakan dapat dibaca tanpa geser samping. Tidak ada perubahan skema, dependensi, atau konfigurasi lingkungan.

## Bukti pemeriksaan

| Perintah/interaksi | Waktu | Hasil | Batas pemeriksaan |
| --- | --- | --- | --- |
| `php artisan test --compact` | 2026-10-10 | 89 tes / 631 assertions lulus | SQLite in-memory; regresi aplikasi |
| `vendor/bin/pint --dirty --format agent` dan `php artisan view:cache --no-interaction` | 2026-10-10 | Lulus | PHP berubah dan kompilasi Blade |
| Browser lokal akun admin/guru pada 390 px dan 1440 px | 2026-10-10 | Dasbor dapat dibuka, urutan menu sesuai, tidak ada overflow horizontal | Lingkungan lokal dengan data yang tersedia; bukan UAT sekolah |
| Browser lokal daftar cerita pada 390 px | 2026-10-10 | Drawer terbuka, menu menuju daftar, filter status bekerja, kartu dan tindakan terlihat tanpa overflow | Akun admin lokal; isi masih contoh |
| `git diff --check` pada berkas UI-005 | 2026-10-10 | Lulus | Perubahan dokumen sekolah milik pengguna berada di luar cakupan |

## Kondisi belum selesai

UAT alur dengan guru dan admin sekolah tetap bagian REL-001. Dokumen `instruction/sekolah/KEBUTUHAN_DATA_DAN_INFORMASI_SEKOLAH.md` yang sudah berubah sebelum sesi ini, serta direktori `docs/` dan `outputs/` yang tidak terlacak, tidak disentuh atau disertakan dalam checkpoint.

## Langkah berikutnya

Pada REL-001, uji satu kegiatan nyata dari penugasan kelas sampai persetujuan publikasi bersama sekolah; catat hambatan antarmuka yang muncul dan perbaiki berdasarkan hasil uji. Lengkapi aturan penilaian resmi, identitas, akun, dan lingkungan operasional sesuai keputusan sekolah.

## Keputusan dan integrasi

Mengikuti D-002, D-003, D-010, D-013, dan D-020. Perubahan berada pada `main` lokal. Remote privat belum dikonfigurasi, sehingga tidak ada push.
