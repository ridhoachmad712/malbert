# Peta dokumen proyek

Mulai sesi agen dari [START_HERE.md](../START_HERE.md). Dokumentasi pendukung disatukan di folder ini; folder lama instruction_docs tidak digunakan lagi.

| Folder | Isi | Kapan dibaca |
| --- | --- | --- |
| [rancangan](rancangan/RENCANA_PENGEMBANGAN_TEBAK_AKSI.md) | Rancangan disepakati, aturan bisnis, tahapan, kriteria selesai | Sebelum mengubah fitur |
| sumber | 14 dokumen Word asli program/sekolah, nama asli dipertahankan | Saat membutuhkan rincian formulir, rubrik dan alur sumber |
| [sekolah](sekolah/KEBUTUHAN_DATA_DAN_INFORMASI_SEKOLAH.md) | Catatan kebutuhan data untuk pihak sekolah | Saat mengumpulkan data dan konfirmasi |
| [kualitas](kualitas/PENERAPAN_ANTI_SLOP.md) | Adaptasi anti-slop, salinan asli dan lisensi | Saat mengembangkan UI, teks, responsivitas |
| [serah-terima](serah-terima/HANDOFF_TEMPLATE.md) | Catatan per tugas dan template | Saat melanjutkan atau mengakhiri tugas |

## Konteks terbaru

- [PROJECT_STATUS.md](../PROJECT_STATUS.md): keadaan terbaru dan langkah berikutnya.
- [TASKS.md](../TASKS.md): status, pemilik, dependensi, dan tautan serah terima tiap tugas.
- [DECISIONS.md](../DECISIONS.md): keputusan dan pertanyaan terbuka.
- [AGENTS.md](../AGENTS.md): aturan bersama; CLAUDE.md mengarahkan ke konteks yang sama.

Instruksi pengguna terbaru dan rancangan yang disepakati mengatur kebutuhan. Dokumen Word adalah sumber referensi; contoh platform di dalamnya tidak otomatis menjadi kewajiban integrasi. Catatan serah terima adalah riwayat, bukan pengganti status terbaru. Anti-slop mengatur kualitas, bukan cakupan bisnis.

## Aturan penyimpanan

Simpan dokumen baru sesuai kategori. Jangan membuat kembali folder instruction_docs atau menggandakan status global. Catatan tugas memakai `serah-terima/HANDOFF_<ID>.md` dan ditautkan dari TASKS.md. Pertahankan dokumen sumber dan salinan upstream; jangan mengeditnya untuk mencatat progres. Jangan menyimpan data murid sebenarnya, kredensial, atau lampiran pengaduan dalam dokumentasi yang dilacak Git.
