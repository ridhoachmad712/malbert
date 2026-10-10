# Catatan keputusan

Keputusan berlaku bersama rancangan sistem. Instruksi pengguna terbaru mengungguli catatan lama; rekam perubahan dan alasan, jangan menghapus riwayat agar keputusan dapat ditelusuri.

| ID | Status | Keputusan dan alasan | Dasar |
| --- | --- | --- | --- |
| D-001 | Diperbarui oleh D-016 | Aplikasi Laravel yang semula bernama Ruang Cerita menjadi ruang publikasi sistem TEBAK AKSI | Rancangan disetujui 2026-09-20 |
| D-002 | Disepakati | Versi awal memakai akun admin/guru; akun murid/orang tua belum termasuk | Rancangan tahap awal |
| D-003 | Disepakati | Pisahkan data operasional dan publikasi; hasil tugas tidak otomatis publik | Rancangan alur pemeriksaan |
| D-004 | Disepakati | Catatan pembinaan/pengaduan dan lampiran privat dibatasi kewenangan | Dokumen program dan rancangan |
| D-005 | Disepakati | Nilai kemampuan dan poin aktivitas terpisah, koreksi terlacak, hindari poin ganda | Rancangan penilaian |
| D-006 | Diterapkan 2026-09-21 | Media publik melalui link folder Drive untuk menghemat penyimpanan; API aktif dan folder nyata berisi 8 foto berhasil diimpor serta ditampilkan tanpa sesi Google | Permintaan pengguna; bukti DRV-001 |
| D-007 | Diadopsi | Anti-slop sebagai rujukan inti lokal selama pengembangan; bukan plugin global | Permintaan pengguna dan PENERAPAN_ANTI_SLOP.md |
| D-008 | Disepakati 2026-09-20 | Konteks disimpan di repositori; AGENTS/CLAUDE mengarah ke sumber yang sama; Git lokal dan serah terima per tugas | Pengguna: “oke buat seperti itu” |
| D-009 | Disepakati 2026-09-20 | Kerja paralel memakai cabang/checkout terpisah dan satu koordinator integrasi | Usulan alur lintas agen disetujui pengguna |
| D-010 | Diterapkan 2026-09-21 | Satu hasil literasi per murid per tugas memuat jurnal, karya, dan refleksi; setiap pengajuan/tinjauan menyimpan snapshot revisi. Guru dapat mengusulkan bacaan internal, admin menyetujui bacaan publik, dan berkas karya hanya tersedia lewat unduhan berizin | Cakupan dan kriteria penerimaan LIT-001; D-003 |
| D-011 | Diterapkan 2026-09-21 | Admin wajib memilih pembulatan nilai, ambang berhasil per murid, periode poin, dan batas pengulangan sebelum penilaian dipakai. Rubrik, poin aktivitas, dan tingkatan apresiasi mengikuti sumber; nilai kemampuan dan poin tetap terpisah. Perubahan penilaian menyimpan snapshot revisi, sedangkan koreksi poin berupa transaksi tambahan dengan alasan sehingga riwayat lama tidak ditulis ulang | Keputusan terbuka dipertahankan sebagai konfigurasi sekolah; kriteria NIL-001; D-005 |
| D-012 | Diterapkan 2026-09-21 | Capaian program hanya dihitung setelah admin memilih periode, cara merangkum nilai murid, populasi penyebut, dan perlakuan data belum dinilai. Target tetap 80%; murid belum dinilai selalu ditampilkan terpisah dan tidak disamakan dengan nilai nol. Laporan bulanan bersifat operasional, sedangkan klaim capaian mengikuti periode monitoring yang dipilih | Kriteria MON-001 dan kebutuhan agar keputusan sekolah tidak di-hardcode |
| D-013 | Diterapkan 2026-09-21 | Publikasi dari bank karya hanya dapat dibuat dari hasil yang disetujui, satu hasil memiliki paling banyak satu publikasi, dan hubungan sumber beserta nomor revisinya disimpan. Isi karya yang dipilih disalin sebagai snapshot publikasi; jurnal, refleksi, umpan balik, nilai, NIS, dan NISN tidak disalin. Kredit kelas menjadi bawaan, sedangkan nama murid memerlukan konfirmasi izin khusus. Versi publik hanya berubah saat admin menyetujui ulang | Kriteria PUB-001; D-003; prinsip kerahasiaan publikasi |
| D-014 | Diterapkan 2026-09-21 | Key Google Drive pada lingkungan lokal dibatasi hanya ke Google Drive API dan disimpan di `.env` yang diabaikan Git. Pembatasan aplikasi belum dipilih karena alamat IP server produksi belum tersedia; pembatasan itu harus ditetapkan saat hosting memiliki alamat keluar tetap | Praktik keamanan key dan kondisi deployment DRV-001 |
| D-015 | Disepakati 2026-09-21 | KAS-001 pembinaan dan pengaduan ditunda karena belum mendesak. Modul tidak diimplementasikan sampai pengguna mengubah prioritas; keputusan akses dan penanganan sensitif tetap terbuka | Instruksi pengguna |
| D-016 | Disepakati 2026-09-23 | Nama aplikasi ditetapkan menjadi **AksiKita** dengan nama penjelas **Portal Literasi, Karya, dan Karakter Siswa**. Nama sekolah tetap menjadi identitas instansi yang terpisah | Instruksi langsung pengguna |
| D-017 | Diterapkan 2026-09-23 | Admin dapat menyesuaikan identitas aplikasi, palet, teks utama, gambar publik, dan favicon melalui field terstruktur dengan pratinjau serta reset bawaan. HTML/CSS bebas tidak disediakan; kombinasi warna teks wajib memenuhi kontras minimum agar keamanan dan keterbacaan tetap terjaga | Permintaan pengguna; UI-002 |
| D-018 | Diterapkan 2026-09-23 | Ruang pengelolaan memakai kerangka yang terpisah dari situs publik: topbar ringkas, sidebar yang dikelompokkan menurut pekerjaan, dan tanpa footer publik. Pada ponsel, sidebar menjadi drawer yang dapat dibuka dan ditutup dari topbar | Permintaan pengguna; UI-003 |
| D-019 | Diterapkan 2026-10-10 | Beranda publik memakai komposisi portofolio: cerita pilihan sebagai fokus, indeks kategori sebagai navigasi, dan sampul cerita dalam susunan editorial. Warna mengikuti tema admin; tipografi besar menonjolkan karya, ruang lapang memisahkan konten, tanpa animasi otomatis agar membaca tetap tenang | Permintaan pengguna agar situs publik terasa seperti portofolio; pemeriksaan UI-004 |

## Masih terbuka

Nilai lengkap identitas sekolah; matriks akses tambahan; nilai konfigurasi sekolah untuk pembulatan, ambang berhasil, periode dan batas pengulangan poin; pilihan konfigurasi nyata untuk periode, metode nilai, populasi, dan data belum dinilai pada capaian 80%; tahap OTFD baku; pengaduan anonim dan waktu respons; retensi lampiran privat; remote privat; rincian penyedia/domain yang menurut pengguna sudah dipilih serta pembuatan layanannya. Rincian dan tahap ketergantungannya berada pada bagian 10 rancangan sistem.

Pilihan konfigurasi pada data demo bukan keputusan final sekolah. Jangan mengisi keputusan terbuka dengan asumsi yang tidak dicatat. Pekerjaan independen boleh berjalan dengan data contoh yang jelas.
