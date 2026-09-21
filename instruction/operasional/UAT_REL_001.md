# Uji penerimaan sekolah REL-001

Gunakan data uji yang disamarkan terlebih dahulu. Catat nama penguji, tanggal, perangkat/browser, hasil, dan nomor temuan. Jangan menulis kata sandi, NISN, data pribadi murid, atau API key pada dokumen ini.

Status yang dipakai: `BELUM`, `LULUS`, atau `TEMUAN`.

| No. | Peran | Skenario dan hasil yang diharapkan | Status | Catatan/temuan |
| --- | --- | --- | --- | --- |
| 1 | Pengunjung | Buka beranda di ponsel; menu, filter, kartu cerita, dan detail dapat digunakan tanpa scroll horizontal | BELUM | |
| 2 | Pengunjung | Buka Pojok Baca dan profil sekolah; informasi sesuai data yang disetujui | BELUM | |
| 3 | Pengunjung | Buka album Drive tanpa login Google; foto/video dan navigasinya dapat digunakan | BELUM | |
| 4 | Admin | Login memakai akun admin nyata, kemudian ganti kata sandi awal | BELUM | |
| 5 | Admin | Isi identitas sekolah, tahun ajaran, semester, rombel, penugasan guru, dan aturan monitoring/penilaian | BELUM | |
| 6 | Admin | Impor CSV murid uji; kesalahan baris terlihat dan dapat diperbaiki tanpa duplikasi | BELUM | |
| 7 | Guru | Login memakai akun guru nyata; hanya rombel yang ditugaskan yang dapat diakses | BELUM | |
| 8 | Guru | Buat bahan bacaan dan tugas, lalu isi jurnal, karya, serta refleksi satu murid uji | BELUM | |
| 9 | Guru/Admin | Ajukan, kembalikan untuk perbaikan, kirim ulang, dan setujui hasil literasi; riwayat revisi tetap tersedia | BELUM | |
| 10 | Guru | Nilai hasil dengan enam indikator dan catat poin aktivitas; nilai dan poin tidak saling mengubah | BELUM | |
| 11 | Guru/Admin | Buka monitoring kelas/murid dan ekspor laporan bulanan; murid belum dinilai tetap dibedakan | BELUM | |
| 12 | Guru/Admin | Siapkan publikasi dari bank karya; jurnal, refleksi, nilai, NIS, dan NISN tidak muncul di halaman publik | BELUM | |
| 13 | Admin | Terbitkan, revisi, arsipkan, dan pulihkan alur publikasi; versi terbit lama tetap tampil sampai revisi disetujui | BELUM | |
| 14 | Admin | Coba input wajib kosong/salah; pesan validasi jelas dan input yang dapat diperbaiki tetap tersedia | BELUM | |
| 15 | Admin | Jalankan backup manual, salin keluar server, dan catat lokasi serta waktu tanpa menulis kredensial | BELUM | |
| 16 | Pengelola server | Pulihkan backup ke database kosong terisolasi, cocokkan jumlah data penting dan media lokal | BELUM | |
| 17 | Pengelola server | Jalankan `php artisan school:release-check`; seluruh baris berstatus `SIAP` | BELUM | |
| 18 | Semua peran | Gunakan aplikasi pada perangkat sekolah selama sesi uji; tidak ada masalah kritis yang menghalangi tugas utama | BELUM | |

## Kriteria persetujuan

- Semua skenario berstatus `LULUS` atau memiliki tindak lanjut yang disepakati sebelum peluncuran.
- Tidak ada temuan kritis pada akses data privat, login, kehilangan data, publikasi, atau pemulihan.
- Admin sekolah, perwakilan guru, dan petugas operasional menyetujui hasil uji.
- Domain, hosting, petugas operasional, jadwal backup, serta lokasi salinan backup sudah dicatat oleh sekolah.

| Persetujuan | Nama/peran | Tanggal | Keputusan/catatan |
| --- | --- | --- | --- |
| Admin sekolah | | | |
| Perwakilan guru | | | |
| Petugas operasional | | | |
