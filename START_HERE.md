# Mulai di sini

Proyek: TEBAK AKSI / AksiKita. Dokumen konteks ini berlaku untuk semua coding-agent dan akun. Riwayat chat tidak diperlukan untuk melanjutkan pekerjaan yang sudah tercatat, tetapi instruksi pengguna terbaru tetap diutamakan.

## Urutan membaca

Peta dokumen pendukung: [instruction/README.md](instruction/README.md). Gunakan lokasi terbaru pada indeks ini, bukan lokasi lama dari percakapan.

1. [AGENTS.md](AGENTS.md): aturan proyek dan Laravel. Jika sudah dimuat otomatis, tidak perlu membacanya dua kali.
2. [PROJECT_STATUS.md](PROJECT_STATUS.md): kondisi terbaru dan langkah berikutnya.
3. [TASKS.md](TASKS.md): tugas, pemilik, dependensi, dan status.
4. [DECISIONS.md](DECISIONS.md): keputusan tetap dan keputusan terbuka.
5. [Rancangan sistem](instruction/rancangan/RENCANA_PENGEMBANGAN_TEBAK_AKSI.md): bagian yang terkait tugas.
6. Catatan serah terima tugas terkait; mulai dari [serah terima awal](instruction/serah-terima/HANDOFF_CTX_001.md).
7. Untuk UI, baca [penerapan anti-slop](instruction/kualitas/PENERAPAN_ANTI_SLOP.md) dan panduan inti yang dirujuknya.

AGENTS.md dan START_HERE.md saling menaut untuk penemuan, bukan instruksi untuk membaca berulang tanpa akhir. Baca setiap dokumen sekali per sesi dan ulangi hanya jika berubah.

## Protokol mulai bekerja

- Periksa `git status --short`, `git branch --show-current`, dan `git log -5 --oneline`. Cocokkan cabang/revisi dengan catatan tugas. Jangan menimpa pekerjaan belum di-commit milik agen lain.
- Bila memakai remote, sinkronkan cabang kerja yang sesuai setelah memastikan perubahan lokal aman. Jangan reset/clean paksa untuk menyamakan konteks.
- Pilih tugas READY yang sudah diotorisasi dan dependensinya selesai. Catat alias agen/sesi, cabang, cakupan file, dan waktu mulai sebelum mengedit.
- Periksa implementasi nyata; dokumen status dapat tertinggal. Laporkan selisih dan perbarui berdasarkan bukti.
- Jangan lanjutkan tugas yang masih dimiliki agen aktif tanpa serah terima. Waktu yang lama saja bukan bukti tugas sudah bebas.

## Pergantian dan kerja paralel

Pergantian berurutan: agen lama menyimpan checkpoint dan serah terima, melepas tugas, kemudian agen baru melanjutkan cabang/revisi yang sama.

Kerja paralel: satu tugas per cabang dan worktree/checkout terpisah. Pisahkan database lokal per checkout; gunakan SQLite in-memory untuk tes sebagaimana konfigurasi proyek. Jangan berbagi database pengembangan untuk migrasi yang belum terkoordinasi.

Satu koordinator mengalokasikan tugas dan menggabungkan perubahan. Agen pekerja memperbarui catatan serah terima tugasnya; koordinator memperbarui status global setelah integrasi. TASKS.md adalah catatan koordinasi, bukan kunci terdistribusi. Cabang yang belum disinkronkan tidak mengetahui klaim agen lain. Tanpa koordinator atau papan tugas bersama, gunakan pergantian berurutan.

Aturan cabang: gunakan nama berbasis ID tugas, misalnya `task/FND-001-kelas`. Tugas dengan perubahan skema/model yang menjadi kontrak bersama diselesaikan dahulu sebelum UI yang bergantung padanya dikerjakan paralel. Jangan merge otomatis hanya karena tes cabang sendiri lulus.

## Sebelum berhenti

1. Catat hasil, berkas berubah, hal tersisa, keputusan, dan kendala pada serah terima per tugas; gunakan [template](instruction/serah-terima/HANDOFF_TEMPLATE.md).
2. Jalankan pemeriksaan yang sesuai; rekam perintah, tanggal, hasil, dan cakupan. Bedakan tes tiruan dengan koneksi eksternal nyata.
3. Periksa diff dan daftar berkas. Commit checkpoint bila layak; jangan menyertakan rahasia. Jika belum commit, tuliskan berkas dan kondisi perubahan yang belum tersimpan dalam Git.
4. Catat cabang dan revisi kode yang diuji. Catatan dalam commit yang sama boleh menyebut “commit yang memuat catatan ini”; agen berikutnya dapat menemukannya dengan `git log -1 -- <path>`.
5. Perbarui status tugas: READY untuk diserahterimakan, REVIEW untuk menunggu integrasi, BLOCKED dengan kendala konkret, DONE hanya setelah kriteria penerimaan terpenuhi dan hasil terintegrasi.
6. Sinkronkan ke remote privat yang telah dikonfigurasi bila diotorisasi. Commit lokal tidak otomatis tersedia di komputer/cloud lain.

## Menjalankan lingkungan baru

- PHP 8.3; versi paket pasti mengikuti `composer.lock`. Jalankan `composer install`, bukan update massal.
- Salin `.env.example` ke `.env` hanya jika `.env` belum ada. Isi konfigurasi database dan jalankan `php artisan key:generate` hanya untuk lingkungan baru.
- Buat database pengembangan tersendiri, lalu `php artisan migrate --no-interaction`. Jangan gunakan `migrate:fresh` pada database berisi pekerjaan/data pengguna.
- Data demo opsional: isi kata sandi demo nonkosong di `.env` sebelum `php artisan db:seed --no-interaction`; jangan gunakan akun demo untuk produksi.
- Jalankan `php artisan serve --host=127.0.0.1 --port=8000`. Periksa server yang sudah hidup sebelum menjalankan duplikat. Port dan proses lokal bukan keadaan yang dibagikan antaragen.
- Tes: `php artisan test --compact`. Konfigurasi tes menggunakan database SQLite in-memory. UI saat ini memakai Blade dan `public/css/app.css` serta `public/js/app.js`; jangan menganggap Vite diperlukan untuk perubahan pada berkas tersebut.
- API key, sesi login Google, database lokal, file unggahan, dan dependensi terpasang tidak berpindah bersama Git. Siapkan terpisah; jangan masukkan nilainya ke catatan konteks.

## Untuk ChatGPT Work atau agen tanpa pembacaan otomatis

Berikan akses ke repositori/cabang terbaru, lalu pasang instruksi ini pada konfigurasi proyek yang tersedia:

> Baca START_HERE.md di akar proyek. Ikuti AGENTS.md, periksa PROJECT_STATUS.md, TASKS.md dan serah terima terbaru. Lanjutkan tugas yang tersedia sesuai otorisasi; perbarui konteks dan bukti pengujian sebelum berhenti.

Jika lingkungan tidak memiliki akses repositori, berikan snapshot kode dan dokumen terbaru. Snapshot unggahan tidak tersinkron otomatis. Akun berbeda memerlukan akses repositori dan konfigurasi lingkungannya sendiri.
