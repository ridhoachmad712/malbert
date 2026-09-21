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
| OPS-001 | Remote privat untuk sinkronisasi lintas lingkungan | BLOCKED | Tidak ada | Tujuan repositori/akun remote belum ditentukan | Belum ada |
| REL-001 | Uji sekolah, pemulihan cadangan, peluncuran | PLANNED | Belum ditugaskan | Modul versi pertama dan pilihan hosting | Belum ada |

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

ID; alias agen/sesi; waktu beserta zona; cabang; revisi dasar; file/area yang dimiliki; dependensi; langkah berikutnya. Koordinator menggabungkan klaim sebelum agen mulai mengedit. Jika cakupan bersinggungan, selesaikan kontrak bersama atau ubah pembagian terlebih dahulu.

## Format pembaruan

Tambahkan tautan `instruction/serah-terima/HANDOFF_<ID>.md`, revisi yang diuji, hasil tes, status integrasi, dan pemilik selanjutnya. Catatan per tugas diperbarui oleh pemilik tugas; ringkasan global oleh koordinator. DONE tidak sama dengan “kode sudah ditulis” atau “sudah commit di cabang pribadi”.
