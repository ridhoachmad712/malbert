# Rencana Pengembangan Sistem TEBAK AKSI

Versi: 1.0  
Tanggal: 20 September 2026  
Status: rancangan pengembangan disepakati pengguna; implementasi bertahap. Keputusan yang masih terbuka ditandai secara eksplisit.

## 1. Tujuan dan kedudukan dokumen

Dokumen ini menjadi rujukan utama pengembangan aplikasi sekolah berdasarkan analisis 14 dokumen Word di `instruction/sumber` dan rancangan yang disepakati dalam percakapan. TEBAK AKSI berarti TEras BAca Kelas Asah Karakter dan literaSI. Aplikasi **AksiKita — Portal Literasi, Karya, dan Karakter Siswa** menjadi ruang publikasi kegiatan, karya, dan prestasi program.

Sistem mendukung kegiatan nyata di sekolah melalui pendataan, dokumentasi, literasi, monitoring, penilaian, pembinaan, pengaduan, dan pelaporan. Tampilan publik harus menarik bagi anak sekolah dasar dan mudah diakses oleh siswa, guru, pengelola, orang tua, masyarakat, serta pihak pemerintah, baik saat berkunjung maupun secara online.

Instruksi pengguna terbaru tetap menjadi acuan ketika ada perubahan. Catat perubahan cakupan atau aturan bisnis di dokumen ini agar implementasi berikutnya tidak memakai keputusan yang sudah usang. Contoh platform pada dokumen sumber bukan kewajiban integrasi dengan semua layanan tersebut.

## 2. Cakupan dan pengguna

| Ruang | Isi | Akses |
| --- | --- | --- |
| Publik | Profil sekolah/program, kegiatan, karya terpilih, prestasi, album, pojok baca digital | Semua pengunjung tanpa login |
| Pengelolaan | Data murid, jurnal, tugas, refleksi, penilaian, monitoring, laporan, pembinaan, pengaduan | Sesuai autentikasi, penugasan, dan kewenangan |

- Tahap awal menggunakan akun admin dan guru. Guru menginput data murid atau mendampingi pengisian kegiatan.
- Akun mandiri murid dan orang tua belum termasuk versi pertama.
- Wali kelas, koordinator, kepala sekolah, dan petugas pengaduan dapat memperoleh kewenangan tambahan sesuai penugasan; rincian matriks hak akses ditetapkan saat modul terkait dikembangkan.
- Guru dibatasi berdasarkan penugasan kelas dan tugasnya. Akses seluruh catatan pembinaan/pengaduan tidak otomatis diberikan kepada semua guru atau semua pengelola konten.
- Nama sekolah dalam sumber: UPTD SPF SD Inpres Mallengkeri Bertingkat, Kota Makassar. Penulisan resmi perlu dikonfirmasi karena sumber juga memakai variasi UPT SPF.
- Fitur komentar publik, akun siswa/orang tua, sinkronisasi otomatis media sosial, dan integrasi penuh Google Forms/Sheets/Classroom tidak menjadi syarat versi pertama.

## 3. Alur kerja utama

### Literasi dan publikasi

Guru menyiapkan bacaan/tugas → murid berkegiatan → guru mencatat jurnal, karya, dan refleksi → pemeriksaan dan perbaikan → penilaian/poin → monitoring → laporan.

Karya terpilih → pemeriksaan kelayakan dan izin publikasi → persetujuan admin → publikasi pada AksiKita.

Hasil tugas tidak otomatis menjadi konten publik. Perubahan pada konten yang sudah terbit menjadi revisi; versi publik tetap memakai hasil yang terakhir disetujui.

### Pembinaan dan pengaduan

Laporan kejadian → verifikasi → penugasan penanganan → sesi pembinaan → kesepakatan perbaikan → pemantauan → evaluasi → selesai atau pembinaan lanjutan.

Satu kasus dapat memiliki beberapa sesi dan tindak lanjut. Pengaduan di luar lingkup program dapat diteruskan kepada pihak terkait dengan pencatatan tindakan. Gunakan bahasa pembinaan dan perbaikan tanpa label negatif pada murid.

## 4. Pemetaan dokumen sumber

| Sumber di instruction/sumber | Kebutuhan sistem |
| --- | --- |
| ONLINE SISTEM.docx | Peta keseluruhan 13 modul program |
| 1.REGISTRASI PENDATAAN MURID.docx | Identitas murid, NIS/NISN, kelas, wali kelas, verifikasi, aktivasi |
| 2.JURNAL LITERASI DIGITAL.docx | Jurnal membaca, durasi, ringkasan, observasi, rubrik, capaian kelas |
| 3. PENGUMPULAN KARYA SISWA.docx | Pengumpulan karya, revisi, verifikasi, penilaian, bank karya |
| 4.REFLEKSI LITERASI DAN KARAKTER.docx | Pemahaman, perasaan, komitmen, refleksi ulang, umpan balik |
| 5.POJOK BACA DIGITAL.docx | Koleksi bacaan, tautan/QR, hubungan bacaan dengan kegiatan |
| 6.TUGAS LITERASI DAN AKSI.docx | Tugas per kelas, tenggat, hasil tugas dan umpan balik |
| 7. PEMBINAAN PESERTA DIDIK.docx | OTFD, catatan kejadian, sesi pembinaan, kesepakatan, perkembangan |
| 8.MONITORING GURU.docx | Rekap murid/kelas/periode dan tindak lanjut |
| 9.PENILAIAN DAN APRESIASI.docx | Poin aktivitas dan tingkatan apresiasi |
| 10.PELAPORAN BULANAN.docx | Rekap bulanan, grafik, cetak/unduh, evaluasi |
| 11.DOKUMENTASI.docx | Arsip menurut kelas, murid, kegiatan, tanggal |
| 12. DISEMINASI.docx | Seleksi dan publikasi karya/capaian program |
| 13. LAYANAN PENGADUAN.docx | Penerimaan, verifikasi, penanganan, monitoring dan penyelesaian |

Catatan interpretasi: urutan Diseminasi/Pengaduan pada dokumen induk berbeda dengan nama berkas. Gunakan nama modul sebagai identitas. Teks sisa seperti “Top of Form”, promosi Codex, dan penawaran membuat diagram bukan kebutuhan sistem.

## 5. Fondasi data dan arsitektur

Lanjutkan aplikasi Laravel yang sudah ada, mengikuti struktur dan konvensi proyek. Jangan mengganti kerangka kerja atau menambah dependensi tanpa mengikuti aturan proyek dan otorisasi pengguna.

Data operasional dibuat terstruktur dan terpisah dari konten publikasi agar dapat dihitung, dilaporkan, dan dibatasi aksesnya. Penamaan tabel/model diputuskan pada implementasi; daftar berikut adalah hubungan bisnis, bukan skema migrasi final.

| Kelompok | Data dan hubungan |
| --- | --- |
| Identitas | Murid, pengguna, tahun ajaran, semester, rombongan belajar, penugasan guru |
| Riwayat kelas | Keanggotaan murid per tahun ajaran; kenaikan kelas tidak menghapus riwayat lama |
| Literasi | Bacaan, tugas, jurnal, karya, refleksi, pemeriksaan dan umpan balik |
| Evaluasi | Observasi per indikator, transaksi poin, koreksi, penghargaan |
| Penanganan | Pengaduan, kejadian, petugas, sesi pembinaan, kesepakatan, pemantauan |
| Publikasi | Dokumentasi dan konten terpilih merujuk kegiatan/karya sumber |

Satu kegiatan dapat menjadi sumber jurnal, karya, refleksi, dan penilaian tanpa mengharuskan guru menginput identitas serta kegiatan yang sama berulang kali. Laporan harus dapat ditelusuri ke catatan sumbernya.

## 6. Aturan bisnis

### Penilaian literasi

Enam indikator: fokus membaca, keaktifan membaca, menjawab pertanyaan, menceritakan kembali, menemukan gagasan pokok, membuat ringkasan/karya. Setiap indikator bernilai 1–4; skor maksimal 24.

Nilai = (jumlah skor / 24) × 100.

Kategori dari sumber: 86–100 sangat aktif/sangat baik; 76–85 aktif/baik; 60–75 cukup; ≤59 perlu bimbingan. Aturan pembulatan nilai perlu ditetapkan sebelum kategori dihitung agar tidak ada celah nilai desimal. Data belum dinilai ditandai “belum dinilai”, bukan nol.

Target program adalah minimal 80% murid mencapai keberhasilan. Ambang keberhasilan per murid, periode pengukuran, penyebut populasi, dan penanganan data belum dinilai masih perlu dibakukan; jangan mengklaim target tercapai sebelum aturan itu ditetapkan.

### Poin dan apresiasi

Poin aktivitas terpisah dari nilai kemampuan literasi.

| Aktivitas dari dokumen sumber | Poin |
| --- | ---: |
| Membaca 15 menit | 5 |
| Membaca dan menceritakan kembali | 10 |
| Membuat ringkasan | 10 |
| Membuat karya/tulisan kreatif | 15 |
| Disiplin dan bertanggung jawab | 5 |
| Membantu teman/bekerja sama | 5 |
| Menerapkan PERMATA | 5 |
| Menyelesaikan tugas refleksi setelah pelanggaran | 10 |

PERMATA dalam sumber berarti Permisi, Maaf, Tabe.

| Total poin | Apresiasi dari sumber |
| --- | --- |
| 20–39 | Bintang Literasi |
| 40–59 | Sahabat Baca |
| 60–79 | Pejuang Karakter |
| 80–99 | Kreator TEBAK AKSI |
| ≥100 | Duta TEBAK AKSI |

- Aktivitas sumber yang sama tidak boleh menghasilkan poin berulang karena pengiriman ulang atau penyimpanan ulang.
- Koreksi poin menyimpan alasan, pelaku, waktu, dan jejak perubahan.
- Usulan periode apresiasi adalah bulanan dengan riwayat tetap tersimpan; ini belum merupakan keputusan final sekolah.
- Batas frekuensi aktivitas, penggabungan beberapa penghargaan untuk satu kegiatan, serta perlakuan poin di bawah 20 dan saat pergantian tahun ajaran perlu ditetapkan.

### Publikasi dan kerahasiaan

- Karya yang dikumpulkan bersifat internal sampai dipilih dan disetujui untuk publikasi.
- NIS/NISN, catatan pribadi, kejadian, pembinaan, dan bukti pengaduan tidak dimasukkan ke galeri publik.
- Publikasi memerlukan pemeriksaan kelayakan konten dan izin yang sesuai.
- Pengaduan memiliki status sumber: diterima → diverifikasi → diproses → ditindaklanjuti → dimonitor → selesai. Rincian penugasan, penolakan/penerusan, dan pembukaan kembali ditetapkan sebelum implementasi modul.

## 7. Strategi media

### Media publik

Guru cukup memasukkan tautan folder Google Drive berisi foto/video. Aplikasi menyimpan metadata dan tautan, bukan salinan file asli. Album ditampilkan sebagai slider.

Perilaku yang sudah dibuat: urutan nama file, maksimal 200 media, hanya isi langsung folder tanpa subfolder, diperbarui saat cerita disimpan ulang. Perubahan mengikuti alur tinjauan admin. Google Drive API dan key lokal aktif; DRV-001 membuktikan 8 foto dari folder nyata dapat diimpor dan ditampilkan tanpa sesi Google.

Unggahan foto terkompresi tetap tersedia sebagai pilihan. Bank karya perlu mendukung tulisan, foto, PDF, serta tautan dokumen, tidak hanya foto/video.

### Media privat

Lampiran pembinaan dan pengaduan memakai penyimpanan privat dengan pemeriksaan hak akses. Jangan menggunakan folder dengan akses publik untuk materi ini. Mekanisme penyimpanan privat final ditentukan pada tahap modul terkait.

Cadangan aplikasi tidak mencakup file asli yang hanya ditautkan dari Drive. Rencana operasional harus mencakup pemeliharaan arsip Drive dan pengujian pemulihan cadangan aplikasi.

## 8. Tahapan dan kriteria penerimaan

### Tahap 1 — Fondasi data dan akses

Pekerjaan: identitas program/sekolah, tahun ajaran, semester, rombongan belajar, data murid, impor data murid, verifikasi/status aktif, penugasan guru, riwayat kelas.

Selesai jika data murid dapat dikelola dan dicari, rombel seperti I A/I B/V A didukung, impor menampilkan kesalahan yang dapat diperbaiki, akses guru mengikuti penugasan, dan pergantian kelas tidak merusak riwayat.

### Tahap 2 — Kegiatan literasi

Pekerjaan: pojok baca, tautan/QR bacaan, jurnal membaca, tugas per kelas dan tenggat, karya, refleksi, pemeriksaan serta revisi.

Selesai jika satu alur bacaan/tugas → jurnal/hasil → pemeriksaan → perbaikan dapat dijalankan, data terhubung ke murid/kelas/periode, dan karya belum otomatis tampil ke publik.

### Tahap 3 — Penilaian dan apresiasi

Pekerjaan: rubrik observasi, nilai otomatis, umpan balik, poin aktivitas, tingkatan apresiasi, riwayat koreksi.

Selesai jika rumus dan kategori sesuai aturan yang telah dibakukan, nilai dan poin terpisah, penyimpanan ulang tidak menggandakan poin, serta koreksi dapat ditelusuri.

### Tahap 4 — Monitoring dan laporan

Pekerjaan: dasbor per murid/kelas/periode, keaktifan, perkembangan literasi, capaian program, laporan bulanan unduh/cetak.

Selesai jika angka laporan cocok dengan data sumber, filter kelas/periode benar, data belum dinilai dibedakan, dan laporan sesuai kewenangan pengguna. Capaian 80% memakai definisi yang telah disepakati.

### Tahap 5 — Pembinaan dan pengaduan

Pekerjaan: formulir pengaduan, verifikasi, penugasan penanganan, OTFD, kesepakatan, sesi lanjutan, pemantauan, penyelesaian.

Selesai jika kasus dapat ditangani dari awal sampai selesai, beberapa sesi dapat dicatat, perubahan status terlacak, serta data dan lampiran tidak dapat diakses pengguna yang tidak berwenang atau pengunjung umum.

### Tahap 6 — Publikasi terintegrasi

Pekerjaan: memilih karya dari bank karya, persetujuan, album Drive, capaian program, tautan berbagi, hubungan publikasi dengan sumber kegiatan.

Selesai jika karya terpilih dapat diterbitkan tanpa membocorkan catatan internal, revisi mempertahankan versi terbit terakhir, dan album folder nyata berhasil dibaca serta ditampilkan.

Publikasi yang sudah ada tetap digunakan selama tahap lain dikembangkan. Bagian integrasi tahap 6 yang dibutuhkan versi pertama dikerjakan setelah tahap 4; tidak perlu menunggu modul pembinaan/pengaduan.

### Tahap 7 — Uji sekolah dan operasional

Pekerjaan: uji bersama guru, penggunaan ponsel, perbaikan alur, pemulihan cadangan, hosting/domain, akun sebenarnya, aktivasi penjadwalan cadangan.

Selesai jika alur utama telah dicoba sekolah, pemulihan cadangan terbukti, konfigurasi lingkungan operasional siap, serta akun dan data contoh ditangani sebelum peluncuran. Pengaturan hosting/deployment mengikuti pilihan pengguna.

## 9. Prioritas versi pertama

Urutan rilis awal: fondasi murid/kelas → jurnal → karya/refleksi → penilaian/poin → monitoring/laporan dasar → publikasi karya terpilih → uji sekolah.

Pembinaan OTFD dan pengaduan menyusul setelah aturan penanganan dan akses dibakukan. Nomor tahap mengelompokkan pekerjaan, bukan kewajiban menunda publikasi sampai tahap 5 selesai.

## 10. Keputusan yang masih terbuka

| Keputusan | Dibutuhkan sebelum |
| --- | --- |
| Nama resmi sekolah, identitas program, daftar kelas dan penugasan | Tahap 1 memakai data sebenarnya |
| Matriks hak akses rinci, termasuk kepala sekolah/koordinator/petugas | Modul terkait mengakses data sebenarnya |
| Nilai konfigurasi sekolah untuk pembulatan dan ambang murid berhasil; periode, penyebut, dan data belum dinilai pada capaian 80% | Penggunaan data nyata dan Tahap 4 |
| Nilai konfigurasi sekolah untuk periode poin dan frekuensi pemberian; aturan penggabungan dan pergantian periode | Penggunaan data nyata dan penyempurnaan Tahap 4 |
| Satu susunan tahap OTFD yang baku dari beberapa versi sumber | Tahap 5 |
| Pengaduan anonim, akses pelapor, penanggung jawab, batas waktu respons | Tahap 5 |
| Penyimpanan privat, retensi dan penghapusan lampiran sensitif | Tahap 5 |
| Konfigurasi Google Drive API dan uji folder nyata | Aktivasi impor album |
| Rincian domain/hosting yang telah dipilih, pembuatan layanan, petugas operasional dan jadwal cadangan | Peluncuran online |

Keputusan terbuka tidak menghalangi pekerjaan lain yang independen. Jangan mengubah usulan menjadi aturan final tanpa dasar dari pengguna/sekolah.

## 11. Kondisi awal dan pelacakan progres

Status berikut dicatat dari pekerjaan sebelum dokumen ini dibuat, bukan verifikasi ulang pada tanggal penulisan.

| Bagian | Status awal |
| --- | --- |
| Laravel, akun admin/guru, pengelolaan konten | Sudah tersedia |
| Galeri kegiatan/karya/prestasi, pencarian, profil/logo sekolah | Sudah tersedia |
| Draf, tinjauan, revisi, publikasi, arsip | Sudah tersedia |
| Foto terkompresi, tautan file Drive | Sudah tersedia |
| Impor folder dan slider | Aktif; folder nyata berisi 8 foto lulus uji API dan browser |
| Cadangan aplikasi | Fitur tersedia; penjadwalan operasional perlu diaktifkan |
| Modul data murid sampai pengaduan | Belum diimplementasikan |
| Pengujian terakhir sebelum dokumen ini | 22 pengujian lulus; bukan bukti koneksi API nyata sudah aktif |
| Deployment publik | Belum dilakukan |

Checklist pengembangan:

- [x] Tahap 1 — Fondasi data dan akses
- [x] Tahap 2 — Kegiatan literasi
- [x] Tahap 3 — Penilaian dan apresiasi
- [x] Tahap 4 — Monitoring dan laporan
- [ ] Tahap 5 — Pembinaan dan pengaduan
- [x] Tahap 6 — Publikasi terintegrasi
- [ ] Tahap 7 — Uji sekolah dan operasional

Sebelum mengerjakan fitur, baca bagian terkait dan periksa kondisi kode saat ini. Setelah perubahan bermakna, perbarui status dengan tanggal, pekerjaan, bukti pengujian, dan hal yang masih tersisa. Tandai selesai hanya jika kriteria penerimaan terpenuhi; jangan menandai koneksi eksternal selesai berdasarkan pengujian dengan respons tiruan saja.

## 12. Standar penyelesaian lintas modul

- Alur normal, validasi, kegagalan penting, dan hak akses telah diuji sesuai dampak perubahan.
- Tampilan dapat digunakan di ponsel; bahasa sederhana dan ramah anak.
- Guru tidak perlu mengisi ulang data yang sudah tersedia tanpa alasan.
- Perubahan status dan penilaian penting dapat ditelusuri.
- Fitur publik tidak membuka data privat melalui halaman, lampiran, atau ekspor.
- Fungsi aplikasi yang sudah berjalan tidak rusak akibat penambahan modul.
- Pengujian memakai data contoh yang jelas; konfigurasi rahasia tidak dimasukkan ke dokumen.
- Ikuti aturan Laravel dan pengujian pada `AGENTS.md`.
- Terapkan [panduan anti-slop proyek](../kualitas/PENERAPAN_ANTI_SLOP.md) selama pengembangan UI dan teks. Pertahankan karakter ramah anak, uji interaksi dan responsivitas, serta laporkan bukti pemeriksaan; adopsi panduan bukan bukti UI sudah lulus audit.

## 13. Riwayat keputusan

| Tanggal | Perubahan | Dasar |
| --- | --- | --- |
| 2026-09-20 | Rancangan TEBAK AKSI versi 1.0 disimpan sebagai rujukan pengembangan | Persetujuan pengguna atas rancangan dan permintaan membuat file rujukan |
| 2026-09-20 | Anti-slop diadopsi sebagai rujukan kualitas lokal dengan revisi sumber tetap dan lisensi | Permintaan pengguna menggunakan repositori miqdadbadjuber/anti-slop |
| 2026-09-21 | Tahap 2 dan 3 selesai; aturan penilaian/poin yang belum dipilih sekolah diterapkan sebagai konfigurasi wajib, bukan nilai baku tersembunyi | Bukti HANDOFF_LIT_001 dan HANDOFF_NIL_001; D-011 |
| 2026-09-21 | Tahap 4 selesai; periode, metode nilai, populasi penyebut, dan perlakuan data belum dinilai menjadi konfigurasi wajib sebelum capaian 80% dihitung | Bukti HANDOFF_MON_001; D-012 |
| 2026-09-21 | Inti tahap 6 untuk bank karya selesai: hanya hasil disetujui dapat dipilih, data internal tidak disalin, izin identitas diperiksa, sumber dan revisi ditelusuri, serta versi terbit dipertahankan saat draf direvisi. Tahap 6 belum ditandai selesai karena album folder nyata masih menunggu DRV-001 | Bukti HANDOFF_PUB_001; D-013 |
| 2026-09-21 | DRV-001 dan Tahap 6 selesai: folder publik nyata menghasilkan 8 foto, seluruh pratinjau dapat diakses tanpa cookie Google, dan slider publik lulus pemeriksaan browser | Bukti HANDOFF_DRV_001; D-006 |
| 2026-09-21 | UI-001 selesai: antarmuka publik, admin, dan guru diaudit pada desktop serta ponsel; perbaikan aksesibilitas, responsivitas, kontras, keadaan error, dan interaksi lulus pengujian regresi | Bukti HANDOFF_UI_001; panduan anti-slop lokal |
| 2026-09-21 | Kesiapan lokal Tahap 7 tersedia: restore cadangan terisolasi lulus, template produksi, pemeriksaan rilis, panduan peluncuran, dan lembar UAT dibuat. Tahap tetap belum selesai sampai hosting/domain dipilih dan UAT sekolah dilaksanakan | Bukti HANDOFF_REL_001; kriteria Tahap 7 |
| 2026-09-23 | Pengguna menyatakan hosting/domain sudah ditetapkan tetapi belum dibuat dan identitas sekolah sudah tersedia. Field NPSN, email, serta telepon resmi ditambahkan agar identitas lengkap dapat dimasukkan tanpa digabung; nilai sebenarnya dan rincian layanan tetap menunggu pengguna | Instruksi pengguna; bukti HANDOFF_REL_001 |
| 2026-09-23 | Nama aplikasi diubah dari Ruang Cerita menjadi AksiKita dengan nama penjelas “Portal Literasi, Karya, dan Karakter Siswa” | Instruksi pengguna; D-016 |
| 2026-09-23 | Admin dapat menyesuaikan tema, teks, gambar publik, dan favicon melalui pengaturan terstruktur dengan pratinjau, validasi kontras, dan reset bawaan | Instruksi pengguna; UI-002; D-017 |

Pekerjaan berikutnya berfokus pada operasi: remote privat, uji sekolah, pemulihan cadangan, hosting/domain, akun nyata, dan penjadwalan cadangan. Tahap 5 ditunda pengguna pada 2026-09-21 karena belum mendesak; aturan penanganan dan akses sensitif tetap perlu ditetapkan sebelum tahap tersebut dimulai kembali.
