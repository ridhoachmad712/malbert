# Serah terima UI-003

- Tanggal/waktu/zona: 2026-09-23 Asia/Makassar
- Agen/sesi: Codex lokal
- Status: DONE
- Tujuan dan kriteria penerimaan: ruang admin/guru tampil sebagai panel pengelolaan yang jelas; header ringkas, menu teratur, menu aktif terbaca, navigasi ponsel berfungsi, dan footer publik tidak muncul pada rute `/kelola`.
- Cabang dan revisi dasar: `main`, dasar `318a857`, commit hasil dapat ditemukan dengan `git log -1 -- instruction/serah-terima/HANDOFF_UI_003.md`.
- Pemilik selanjutnya: tidak ada.

## Hasil

- Layout pengelolaan dipisahkan dari header, strip demo, dan footer situs publik.
- Topbar pengelolaan setinggi 64 px pada desktop dan 58 px pada ponsel memuat identitas aplikasi, tautan situs publik, peran pengguna, dan tombol menu ponsel.
- Sidebar dikelompokkan menjadi Ringkasan, Konten sekolah, Pembelajaran, Administrasi, dan Akun. Kelompok Administrasi hanya dirender untuk admin.
- Setiap rute utama memiliki penanda menu aktif, termasuk editor cerita, bank karya, data murid, literasi, penilaian, monitoring, fondasi, pengguna, profil, tampilan, dan akun.
- Identitas pengguna dan tombol keluar ditempatkan di dasar sidebar. Pada ponsel, sidebar menjadi drawer vertikal yang dapat ditutup melalui tautan atau tombol Escape.
- Overflow horizontal pada drawer dicegah; area konten tetap memakai komponen dan palet yang sudah tersedia.

## Bukti pemeriksaan

| Pemeriksaan | Hasil | Cakupan |
| --- | --- | --- |
| `php artisan test --compact` | PASS, 86 tes / 608 assertions | Regresi aplikasi dan layout admin/guru tanpa footer publik |
| `vendor/bin/pint --dirty --format agent` | PASS | PHP dan Blade terkait |
| `node --check public/js/app.js` | PASS | Kontrol menu publik dan drawer pengelolaan |
| `php artisan view:cache` | PASS | Kompilasi seluruh Blade; cache kemudian dibersihkan |
| Browser ponsel | PASS | Dasbor, Tampilan & Konten, topbar, drawer, kelompok menu, penanda aktif, dan overflow |

## Delivery Gate UI

- Hard Gate: tidak ada footer publik pada halaman pengelolaan; kontrol drawer memiliki label, status `aria-expanded`, sasaran sentuh, dan dukungan Escape.
- Purpose-Gate: topbar hanya memuat konteks kerja serta akses kembali ke situs; sidebar dikelompokkan berdasarkan alur kerja pengguna.
- Liveliness: identitas AksiKita tetap hadir melalui warna merek dan penanda aktif tanpa dekorasi tambahan yang mengganggu pekerjaan.
- Craftsmanship: menu admin dan guru mengikuti akses masing-masing, responsif, tidak meluap secara horizontal, dan diuji dengan regresi.

## Batas dan langkah berikutnya

- Isi masing-masing halaman pengelolaan tidak dirombak; UI-003 berfokus pada kerangka global, navigasi, header, dan footer.
- Pemeriksaan browser langsung sesi ini menggunakan viewport panel ponsel. Jalur desktop dilindungi oleh breakpoint CSS dan regresi Blade, tetapi pemeriksaan visual desktop ulang dapat dimasukkan ke UAT sekolah sebelum rilis.
- Perubahan lokal pengguna pada `instruction/sekolah/KEBUTUHAN_DATA_DAN_INFORMASI_SEKOLAH.md` dipertahankan dan tidak termasuk dalam checkpoint UI-003.
