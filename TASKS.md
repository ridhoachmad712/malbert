# Papan tugas bersama

Status: READY = dapat diambil; IN_PROGRESS = dikerjakan; REVIEW = menunggu pemeriksaan/integrasi; BLOCKED = ada kendala konkret; PLANNED = belum siap/dependensi belum selesai; DONE = terintegrasi dan memenuhi kriteria.

Koordinator harus membagikan tugas sebelum kerja paralel. Klaim dalam berkas ini tidak merupakan pengunci otomatis lintas checkout. Gunakan alias agen, bukan email/kredensial akun.

| ID | Tugas | Status | Pemilik aktif | Dependensi/kendala | Serah terima |
| --- | --- | --- | --- | --- | --- |
| DOC-002 | Satukan dan kategorikan dokumentasi proyek | DONE | Tidak ada | Sumber utuh dan tautan diperbarui | [Catatan](instruction/serah-terima/HANDOFF_DOC_002.md) |
| CTX-001 | Konteks lintas agen dan Git lokal | DONE | Tidak ada | Selesai pada checkpoint awal | [Catatan](instruction/serah-terima/HANDOFF_CTX_001.md) |
| DOC-001 | Simpan catatan kebutuhan sekolah dalam Markdown | DONE | Tidak ada | Sesuai permintaan folder instruction | [Catatan](instruction/serah-terima/HANDOFF_DOC_001.md) |
| FND-001 | Tahap 1: fondasi murid, rombel, tahun ajaran, penugasan | DONE | Tidak ada | Terintegrasi setelah 48 tes dan QA admin/guru lulus | [Catatan](instruction/serah-terima/HANDOFF_FND_001.md) |
| LIT-001 | Tahap 2: bacaan, jurnal, tugas, karya, refleksi | DONE | Tidak ada | Terintegrasi setelah 57 tes dan QA Pojok Baca lulus | [Catatan](instruction/serah-terima/HANDOFF_LIT_001.md) |
| NIL-001 | Tahap 3: observasi, poin, apresiasi | DONE | Tidak ada | Terintegrasi setelah 66 tes dan QA halaman penilaian lulus | [Catatan](instruction/serah-terima/HANDOFF_NIL_001.md) |
| MON-001 | Tahap 4: monitoring dan laporan | DONE | Tidak ada | Terintegrasi setelah 73 tes dan QA monitoring/laporan lulus | [Catatan](instruction/serah-terima/HANDOFF_MON_001.md) |
| KAS-001 | Tahap 5: pembinaan dan pengaduan | PLANNED | Tidak ada | Ditunda pengguna karena belum mendesak; aturan penanganan dan akses tetap belum ditetapkan | Belum ada |
| PUB-001 | Tahap 6: publikasi terhubung bank karya | DONE | Tidak ada | Integrasi bank karya dan album Drive nyata selesai | [Catatan](instruction/serah-terima/HANDOFF_PUB_001.md) |
| DRV-001 | Aktivasi API dan uji album folder nyata | DONE | Tidak ada | Folder nyata berisi 8 foto berhasil diimpor dan ditampilkan tanpa sesi Google | [Catatan](instruction/serah-terima/HANDOFF_DRV_001.md) |
| UI-001 | Audit UI publik, admin, dan guru | DONE | Tidak ada | 21 rute desktop/ponsel, keyboard, kontras, keadaan UI, dan regresi lulus | [Catatan](instruction/serah-terima/HANDOFF_UI_001.md) |
| UI-002 | Pengaturan tampilan dan konten publik | DONE | Tidak ada | Admin dapat mengubah tema, teks, gambar, favicon, melihat pratinjau, dan memulihkan bawaan | [Catatan](instruction/serah-terima/HANDOFF_UI_002.md) |
| UI-003 | Rapikan kerangka halaman pengelolaan | DONE | Tidak ada | Header ringkas, menu berkelompok, navigasi ponsel, dan halaman pengelolaan tanpa footer publik | [Catatan](instruction/serah-terima/HANDOFF_UI_003.md) |
| OPS-001 | Remote privat untuk sinkronisasi lintas lingkungan | BLOCKED | Tidak ada | Tujuan repositori/akun remote belum ditentukan | Belum ada |
| REL-001 | Uji sekolah, pemulihan cadangan, peluncuran | BLOCKED | Tidak ada | Branding AksiKita selesai; menunggu nilai identitas, rincian pilihan serta pembuatan hosting/domain, dan UAT sekolah | [Catatan](instruction/serah-terima/HANDOFF_REL_001.md) |

## Format klaim tugas

FND-001; Codex pemeriksaan lanjutan; 2026-09-20 Asia/Makassar; main; dasar f1b38d9 beserta perubahan lokal fondasi; tes fondasi, pemeriksaan browser, perbaikan yang terbukti dan konteks; tanpa pekerja paralel. Pengguna meminta melanjutkan setelah analisis. Sesi sebelumnya terverifikasi berstatus systemError, bukan aktif. Langkah berikutnya: verifikasi batas periode/riwayat, antarmuka, dan checkpoint.

Riwayat checkpoint pemeriksaan 2026-09-20: kepemilikan sempat dilepas dengan status REVIEW; lihat HANDOFF_FND_001. Pada saat itu perubahan masih lokal dan belum di-commit.

FND-001; Codex finalisasi; 2026-09-21 Asia/Makassar; main; dasar f1b38d9 + working tree tercatat; seluruh paket fondasi untuk tinjauan/integrasi, QA browser dan konteks; pengguna meminta lanjut menyelesaikan fondasi dan checkpoint. Database QA terpisah, tanpa pekerja paralel.

Finalisasi 2026-09-21: QA admin/guru dan impor CSV selesai, bug periode berjalan diperbaiki, 48 tes / 331 assertions lulus. Kepemilikan dilepas setelah commit checkpoint; LIT-001 menjadi READY.

LIT-001; Codex lokal; 2026-09-21 Asia/Makassar; main; dasar 476d080; kontrak data bacaan/tugas/jurnal-karya-refleksi/pemeriksaan, model/migrasi, controller/request, media privat, halaman publik dan kelola, routes, backup, factories/seeder, tes dan konteks; tanpa pekerja paralel. Pengguna meminta mengerjakan LIT-001.

Finalisasi 2026-09-21: alur bacaan/tugas → jurnal-karya-refleksi → tinjauan → perbaikan/persetujuan selesai; karya tetap privat dan periode disimpan sebagai snapshot. 57 tes / 398 assertions, Pint, sintaks JavaScript, rute, diff check, dan QA visual Pojok Baca lulus. Kepemilikan dilepas setelah commit checkpoint; NIL-001 masih menunggu keputusan penilaian terbuka.

NIL-001; Codex lokal; 2026-09-21 Asia/Makassar; main; dasar 6113431; konfigurasi aturan sekolah, rubrik enam indikator, nilai/kategori/umpan balik, transaksi poin, tingkatan apresiasi, koreksi terlacak, routes/views/backup/tests/konteks; tanpa pekerja paralel. Aturan terbuka tidak di-hardcode: admin wajib menetapkannya sebelum modul dipakai. Pengguna meminta lanjut ke tahap berikutnya.

Finalisasi 2026-09-21: rubrik enam indikator, nilai otomatis, umpan balik, poin aktivitas terpisah, apresiasi per periode, pencegahan poin ganda, dan koreksi terlacak selesai. 66 tes / 460 assertions, Pint, sintaks JavaScript, rute, diff check, dan QA visual responsif lulus. Kepemilikan dilepas setelah commit checkpoint; MON-001 tetap PLANNED sampai definisi capaian program 80% dibakukan.

MON-001; Codex lokal; 2026-09-21 Asia/Makassar; main; dasar f254c2c; konfigurasi definisi capaian 80%, dasbor per kelas/murid/periode, data belum dinilai, laporan bulanan cetak/unduh, akses, routes/views/tests/backup/konteks; tanpa pekerja paralel. Pengguna meminta melanjutkan. Nilai aturan sekolah tidak akan di-hardcode atau disimpulkan dari data demo.

Finalisasi 2026-09-21: definisi capaian menjadi konfigurasi wajib, dasbor kelas/periode, perkembangan murid, laporan bulanan, cetak, dan CSV selesai. 73 tes / 497 assertions, Pint, sintaks JavaScript, 65 rute, diff check, dan QA browser responsif lulus. Kepemilikan dilepas setelah commit checkpoint; PUB-001 menjadi READY sementara KAS-001 masih menunggu aturan penanganan.

PUB-001; Codex lokal; 2026-09-21 Asia/Makassar; main; dasar 2a5fb34; pemilihan karya internal yang disetujui, pemeriksaan kelayakan dan izin, pembuatan draf publikasi, hubungan sumber yang tetap, revisi/versi publik, media yang aman, routes/views/backup/tests/konteks; tanpa pekerja paralel. Pengguna meminta mengerjakan PUB-001. Catatan jurnal, refleksi, umpan balik, nilai, serta berkas privat tidak boleh ikut dipublikasikan.

Finalisasi 2026-09-21: bank karya yang disetujui dapat dibuat menjadi satu draf publikasi yang terhubung sumber dan nomor revisinya. Salinan karya terpilih terpisah dari berkas privat, kredit nama membutuhkan konfirmasi izin khusus, dan revisi tidak mengganti versi yang sedang terbit. 77 tes / 541 assertions, Pint, 68 rute, diff check, dan QA browser desktop/390 px lulus. Kepemilikan dilepas setelah commit checkpoint; tahap 6 keseluruhan belum dicentang karena album Drive nyata tetap DRV-001 dan masih BLOCKED.

DRV-001; Codex lokal; 2026-09-21 16:23 Asia/Makassar; main; dasar 62a1ecf; aktivasi Google Drive API, pembuatan key terbatas Drive API, konfigurasi lokal, uji koneksi nyata, uji folder publik dan pembaruan konteks; tanpa pekerja paralel. Google Drive API dan key lokal sudah aktif. Kriteria penerimaan folder nyata menunggu tautan folder publik berisi foto/video dari pengguna.

Finalisasi 2026-09-21 16:46: folder publik nyata dari pengguna menghasilkan 8 foto. Seluruh URL pratinjau merespons 200 tanpa cookie Google; halaman publik memuat 8 slide dan navigasi berpindah dari 1/8 ke 2/8. Publikasi QA sementara dihapus. 6 tes Drive / 55 assertions dan regresi 77 tes / 541 assertions lulus. Kepemilikan dilepas; DRV-001 dan Tahap 6 selesai.

UI-001; Codex lokal; 2026-09-21 Asia/Makassar; main; dasar 4c1563f; audit halaman publik/admin/guru pada desktop dan ponsel, keyboard, keadaan kosong/validasi, konsol, perbaikan CSS/Blade/JavaScript yang terbukti, tes dan konteks; tanpa pekerja paralel. Pengguna meminta audit UI dikerjakan lebih dahulu.

Finalisasi 2026-09-21: 21 rute representatif publik/admin diperiksa pada 1440×900 dan 390×844; 10 rute guru diperiksa pada kedua ukuran. Overflow dasbor, kontras teks sekunder, sasaran sentuh, semantik dekorasi, navigasi seluler, placeholder tabel, dan kegagalan prasyarat laporan bulanan diperbaiki. 77 tes / 544 assertions, Pint, sintaks JavaScript, 68 rute, diff check, pemeriksaan kontras, keyboard, dan konsol browser lulus. Kepemilikan dilepas setelah commit checkpoint; REL-001 menjadi tahapan produk berikutnya, sedangkan OPS-001 tetap menunggu tujuan remote.

REL-001; Codex lokal; 2026-09-21 Asia/Makassar; main; dasar 5eca5e6; simulasi pemulihan cadangan terisolasi, pemeriksaan konfigurasi produksi dan scheduler, paket UAT sekolah, penanganan akun/data contoh, bukti pengujian, dan konteks; tanpa pekerja paralel. Pengguna meminta mengerjakan REL-001. Hosting/domain dan UAT bersama sekolah tidak akan dianggap selesai sebelum pilihan serta pelaksanaannya nyata.

Checkpoint 2026-09-21: pemulihan nyata ke SQLite/penyimpanan terisolasi lulus pada 17 tabel tanpa selisih; template lingkungan produksi, panduan peluncuran, lembar UAT, dan perintah `school:release-check` tersedia. 79 tes / 548 assertions dan pemeriksaan platform/scheduler/rute lulus. Konfigurasi cache tes dipisahkan agar tes tidak dapat membaca cache konfigurasi lokal; database lokal yang sempat kosong saat verifikasi paralel dipulihkan penuh dari backup pra-uji menjadi 2 pengguna, 7 publikasi, dan 1 profil sekolah. Tugas dilepas dengan status BLOCKED sampai hosting/domain dipilih, akun/data nyata siap, dan UAT sekolah dilaksanakan.

REL-001; Codex lokal; 2026-09-22 Asia/Makassar; main; dasar be1fea4; perbandingan hosting/domain, rekomendasi deployment awal, pembaruan panduan dan konteks; tanpa pekerja paralel. Pengguna meminta melanjutkan REL-001. Pengadaan layanan tidak dilakukan sebelum opsi dan kepemilikan akun ditetapkan pengguna.

Checkpoint 2026-09-22: Hostinger Unlimited direkomendasikan secara bersyarat untuk rilis awal, dengan Hostinger Premium sebagai opsi pilot hemat. Domain memakai `.sch.id` milik sekolah dan subdomain `tebakaksi`; bila domain sudah ada, tidak perlu membeli domain kedua. Daftar enam konfirmasi pra-pembayaran, risiko Laravel 13, perbandingan biaya perpanjangan, dan alternatif cloud/VPS telah dicatat. Kepemilikan dilepas dengan status BLOCKED sampai pengguna menetapkan layanan, staging tersedia, dan sekolah siap UAT.

REL-001; Codex lokal; 2026-09-23 Asia/Makassar; main; dasar e746284; kelengkapan field identitas sekolah, validasi, tampilan profil publik, tes, dan konteks; tanpa pekerja paralel. Pengguna menyatakan hosting/domain sudah ditetapkan tetapi belum dibuat dan identitas sekolah sudah tersedia. Nilai identitas belum diberikan, sehingga aplikasi disiapkan untuk menerimanya tanpa menebak data.

Checkpoint 2026-09-23: profil sekolah kini menampung NPSN delapan digit, email resmi, dan telepon resmi secara terpisah selain nama, alamat, tagline, deskripsi, visi, misi, dan logo. Field divalidasi, tampil pada profil publik, dan menjadi bagian pemeriksaan rilis. 80 tes / 555 assertions dan Pint lulus; profil publik diperiksa pada desktop serta viewport ponsel. Kepemilikan dilepas dengan status BLOCKED sampai nilai identitas dan rincian hosting/domain diberikan, layanan dibuat, lalu staging/UAT dapat dijalankan.

REL-001; Codex lokal; 2026-09-23 Asia/Makassar; main; dasar c7c3306 dengan perubahan lokal pengguna pada dokumen kebutuhan sekolah yang tidak disentuh; nama dan teks identitas aplikasi, konfigurasi contoh, tes visual/regresi, keputusan dan konteks; tanpa pekerja paralel. Pengguna menetapkan AksiKita dengan nama penjelas “Portal Literasi, Karya, dan Karakter Siswa”.

Checkpoint branding 2026-09-23: nama AksiKita diterapkan pada header, judul halaman, metadata, halaman utama, halaman masuk, footer, laporan cetak, konfigurasi lokal, dan template produksi. Nama penjelas tampil pada halaman utama, footer, dan metadata; nama sekolah tetap menjadi identitas instansi. 80 tes / 557 assertions dan Pint lulus. Halaman utama desktop/390 px serta halaman masuk 390 px lulus pemeriksaan browser tanpa overflow. Perubahan lokal pengguna pada dokumen kebutuhan sekolah dipertahankan di luar commit. Kepemilikan dilepas dan REL-001 kembali BLOCKED pada data/deployment/UAT.

UI-002; Codex lokal; 2026-09-23 Asia/Makassar; main; dasar 9c3c64b dengan perubahan lokal pengguna pada dokumen kebutuhan sekolah yang tidak disentuh; panel admin Tampilan & Konten, pengaturan tema/teks/gambar, pratinjau, reset, akses, tes dan konteks; tanpa pekerja paralel. Pengguna meminta seluruh isi UI publik dapat disesuaikan seperti warna, teks, dan foto.

Finalisasi UI-002 2026-09-23: admin dapat mengatur nama aplikasi, nama penjelas, tujuh warna dengan validasi kontras, teks halaman utama/login/footer, gambar utama/login/profil, dan favicon. Pratinjau berubah langsung sebelum disimpan; reset mengembalikan bawaan tanpa mengubah data sekolah. Penyimpanan profil sekolah terbukti mempertahankan tema. 85 tes / 597 assertions, Pint, sintaks JavaScript, kompilasi Blade, rute, dan browser desktop/390 px lulus. Perubahan lokal pengguna pada dokumen kebutuhan sekolah tetap di luar commit. Kepemilikan dilepas dan UI-002 selesai.

UI-003; Codex lokal; 2026-09-23 Asia/Makassar; main; dasar 318a857 dengan perubahan lokal pengguna pada dokumen kebutuhan sekolah yang tidak disentuh; layout pengelolaan, navigasi admin/guru, header, responsivitas, tes dan konteks; tanpa pekerja paralel. Pengguna meminta halaman admin dirapikan karena belum tampak seperti admin panel, menu berantakan, header terlalu tinggi, dan footer publik tidak diperlukan.

Finalisasi UI-003 2026-09-23: halaman pengelolaan memakai topbar 64 px, sidebar berkelompok berdasarkan tugas, penanda menu aktif yang konsisten, identitas pengguna ringkas, serta menu drawer pada ponsel. Footer dan strip demo publik tidak dirender pada rute pengelolaan. 86 tes / 608 assertions, Pint, sintaks JavaScript, kompilasi Blade, dan pemeriksaan browser ponsel lulus; drawer dapat dibuka tanpa overflow horizontal. Perubahan lokal pengguna pada dokumen kebutuhan sekolah tetap di luar checkpoint. Kepemilikan dilepas dan UI-003 selesai.

ID; alias agen/sesi; waktu beserta zona; cabang; revisi dasar; file/area yang dimiliki; dependensi; langkah berikutnya. Koordinator menggabungkan klaim sebelum agen mulai mengedit. Jika cakupan bersinggungan, selesaikan kontrak bersama atau ubah pembagian terlebih dahulu.

## Format pembaruan

Tambahkan tautan `instruction/serah-terima/HANDOFF_<ID>.md`, revisi yang diuji, hasil tes, status integrasi, dan pemilik selanjutnya. Catatan per tugas diperbarui oleh pemilik tugas; ringkasan global oleh koordinator. DONE tidak sama dengan “kode sudah ditulis” atau “sudah commit di cabang pribadi”.
