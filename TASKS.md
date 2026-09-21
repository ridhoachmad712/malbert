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
| NIL-001 | Tahap 3: observasi, poin, apresiasi | PLANNED | Belum ditugaskan | LIT-001 dan aturan penilaian terbuka | Belum ada |
| MON-001 | Tahap 4: monitoring dan laporan | PLANNED | Belum ditugaskan | NIL-001 dan definisi target | Belum ada |
| KAS-001 | Tahap 5: pembinaan dan pengaduan | PLANNED | Belum ditugaskan | Fondasi akses dan keputusan penanganan | Belum ada |
| PUB-001 | Tahap 6: publikasi terhubung bank karya | PLANNED | Belum ditugaskan | Bank karya; tidak wajib menunggu KAS-001 | Belum ada |
| DRV-001 | Aktivasi API dan uji album folder nyata | BLOCKED | Tidak ada | Akses Google Cloud dan konfigurasi key belum selesai | Lihat PROJECT_STATUS.md |
| OPS-001 | Remote privat untuk sinkronisasi lintas lingkungan | BLOCKED | Tidak ada | Tujuan repositori/akun remote belum ditentukan | Belum ada |
| REL-001 | Uji sekolah, pemulihan cadangan, peluncuran | PLANNED | Belum ditugaskan | Modul versi pertama dan pilihan hosting | Belum ada |

## Format klaim tugas

FND-001; Codex pemeriksaan lanjutan; 2026-09-20 Asia/Makassar; main; dasar f1b38d9 beserta perubahan lokal fondasi; tes fondasi, pemeriksaan browser, perbaikan yang terbukti dan konteks; tanpa pekerja paralel. Pengguna meminta melanjutkan setelah analisis. Sesi sebelumnya terverifikasi berstatus systemError, bukan aktif. Langkah berikutnya: verifikasi batas periode/riwayat, antarmuka, dan checkpoint.

Riwayat checkpoint pemeriksaan 2026-09-20: kepemilikan sempat dilepas dengan status REVIEW; lihat HANDOFF_FND_001. Pada saat itu perubahan masih lokal dan belum di-commit.

FND-001; Codex finalisasi; 2026-09-21 Asia/Makassar; main; dasar f1b38d9 + working tree tercatat; seluruh paket fondasi untuk tinjauan/integrasi, QA browser dan konteks; pengguna meminta lanjut menyelesaikan fondasi dan checkpoint. Database QA terpisah, tanpa pekerja paralel.

Finalisasi 2026-09-21: QA admin/guru dan impor CSV selesai, bug periode berjalan diperbaiki, 48 tes / 331 assertions lulus. Kepemilikan dilepas setelah commit checkpoint; LIT-001 menjadi READY.

LIT-001; Codex lokal; 2026-09-21 Asia/Makassar; main; dasar 476d080; kontrak data bacaan/tugas/jurnal-karya-refleksi/pemeriksaan, model/migrasi, controller/request, media privat, halaman publik dan kelola, routes, backup, factories/seeder, tes dan konteks; tanpa pekerja paralel. Pengguna meminta mengerjakan LIT-001.

Finalisasi 2026-09-21: alur bacaan/tugas → jurnal-karya-refleksi → tinjauan → perbaikan/persetujuan selesai; karya tetap privat dan periode disimpan sebagai snapshot. 57 tes / 398 assertions, Pint, sintaks JavaScript, rute, diff check, dan QA visual Pojok Baca lulus. Kepemilikan dilepas setelah commit checkpoint; NIL-001 masih menunggu keputusan penilaian terbuka.

ID; alias agen/sesi; waktu beserta zona; cabang; revisi dasar; file/area yang dimiliki; dependensi; langkah berikutnya. Koordinator menggabungkan klaim sebelum agen mulai mengedit. Jika cakupan bersinggungan, selesaikan kontrak bersama atau ubah pembagian terlebih dahulu.

## Format pembaruan

Tambahkan tautan `instruction/serah-terima/HANDOFF_<ID>.md`, revisi yang diuji, hasil tes, status integrasi, dan pemilik selanjutnya. Catatan per tugas diperbarui oleh pemilik tugas; ringkasan global oleh koordinator. DONE tidak sama dengan “kode sudah ditulis” atau “sudah commit di cabang pribadi”.
