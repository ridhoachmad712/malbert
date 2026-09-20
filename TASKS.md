# Papan tugas bersama

Status: READY = dapat diambil; IN_PROGRESS = dikerjakan; REVIEW = menunggu pemeriksaan/integrasi; BLOCKED = ada kendala konkret; PLANNED = belum siap/dependensi belum selesai; DONE = terintegrasi dan memenuhi kriteria.

Koordinator harus membagikan tugas sebelum kerja paralel. Klaim dalam berkas ini tidak merupakan pengunci otomatis lintas checkout. Gunakan alias agen, bukan email/kredensial akun.

| ID | Tugas | Status | Pemilik aktif | Dependensi/kendala | Serah terima |
| --- | --- | --- | --- | --- | --- |
| CTX-001 | Konteks lintas agen dan Git lokal | DONE | Tidak ada | Selesai pada checkpoint awal | [Catatan](instruction_docs/HANDOFF_CTX_001.md) |
| DOC-001 | Simpan catatan kebutuhan sekolah dalam Markdown | DONE | Tidak ada | Sesuai permintaan folder instruction | [Catatan](instruction_docs/HANDOFF_DOC_001.md) |
| FND-001 | Tahap 1: fondasi murid, rombel, tahun ajaran, penugasan | READY | Belum ditugaskan | Mulai dari kontrak data dan pembagian subtugas | Belum ada |
| LIT-001 | Tahap 2: bacaan, jurnal, tugas, karya, refleksi | PLANNED | Belum ditugaskan | FND-001 | Belum ada |
| NIL-001 | Tahap 3: observasi, poin, apresiasi | PLANNED | Belum ditugaskan | LIT-001 dan aturan penilaian terbuka | Belum ada |
| MON-001 | Tahap 4: monitoring dan laporan | PLANNED | Belum ditugaskan | NIL-001 dan definisi target | Belum ada |
| KAS-001 | Tahap 5: pembinaan dan pengaduan | PLANNED | Belum ditugaskan | Fondasi akses dan keputusan penanganan | Belum ada |
| PUB-001 | Tahap 6: publikasi terhubung bank karya | PLANNED | Belum ditugaskan | Bank karya; tidak wajib menunggu KAS-001 | Belum ada |
| DRV-001 | Aktivasi API dan uji album folder nyata | BLOCKED | Tidak ada | Akses Google Cloud dan konfigurasi key belum selesai | Lihat PROJECT_STATUS.md |
| OPS-001 | Remote privat untuk sinkronisasi lintas lingkungan | BLOCKED | Tidak ada | Tujuan repositori/akun remote belum ditentukan | Belum ada |
| REL-001 | Uji sekolah, pemulihan cadangan, peluncuran | PLANNED | Belum ditugaskan | Modul versi pertama dan pilihan hosting | Belum ada |

## Format klaim tugas

ID; alias agen/sesi; waktu beserta zona; cabang; revisi dasar; file/area yang dimiliki; dependensi; langkah berikutnya. Koordinator menggabungkan klaim sebelum agen mulai mengedit. Jika cakupan bersinggungan, selesaikan kontrak bersama atau ubah pembagian terlebih dahulu.

## Format pembaruan

Tambahkan tautan `instruction_docs/HANDOFF_<ID>.md`, revisi yang diuji, hasil tes, status integrasi, dan pemilik selanjutnya. Catatan per tugas diperbarui oleh pemilik tugas; ringkasan global oleh koordinator. DONE tidak sama dengan “kode sudah ditulis” atau “sudah commit di cabang pribadi”.
