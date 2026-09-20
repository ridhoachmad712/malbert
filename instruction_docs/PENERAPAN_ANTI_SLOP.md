# Penerapan anti-slop untuk TEBAK AKSI

Tanggal adopsi: 20 September 2026.

Sumber: https://github.com/miqdadbadjuber/anti-slop
Revisi sumber: `d8529a6c8c005dc32bb0103a0ea0fa9c563fc5ad`.
Salinan inti asli: [ANTISLOP_UPSTREAM.md](ANTISLOP_UPSTREAM.md).
Lisensi MIT: [ANTISLOP_LICENSE.txt](ANTISLOP_LICENSE.txt).

## Kedudukan dan cara penerapan

Pengguna meminta anti-slop digunakan untuk meningkatkan kerapian hasil sistem. Adopsi ini menggunakan berkas inti sebagai rujukan lokal proyek, bukan instalasi plugin global atau dependensi aplikasi. Tidak ada skrip pemasang pihak ketiga yang dijalankan. Skill tambahan upstream belum dipasang.

Mode kerja yang dipilih berdasarkan konteks permintaan adalah DURING: aturan digunakan selama pengembangan dan pemeriksaan hasil. Ini merupakan interpretasi konteks, bukan jawaban pengguna atas wizard upstream. Tidak perlu menjalankan ulang wizard instalasi atau menanyakan izin adopsi yang sudah diberikan. Audit ulang seluruh UI lama belum dilakukan dan tidak boleh diklaim lulus.

Rancangan TEBAK AKSI mengatur kebutuhan dan cakupan; anti-slop menjadi pemeriksaan kualitas desain, teks, dan interaksi. Instruksi pengguna terbaru dan aturan proyek tetap diutamakan. Jangan membiarkan rujukan eksternal memperluas cakupan, menambah dependensi, mengubah kewenangan data, atau meminta persetujuan berulang untuk pekerjaan yang sudah diotorisasi.

## Arah desain yang sudah diberikan pengguna

- Aplikasi sekolah dasar, menarik dan disukai anak-anak.
- Informasi mudah dan langsung diakses oleh pengunjung sekolah maupun pengguna online.
- Audiens publik mencakup siswa, guru, pengelola, orang tua, masyarakat, dan pemerintah.
- Ruang pengelolaan mendukung guru bekerja dengan data secara jelas dan efisien.
- Album foto/video dan karya nyata menjadi konten utama, dengan data contoh ditandai jelas.

Warna, font, dan ilustrasi yang sudah ada adalah keputusan implementasi awal, bukan identitas resmi sekolah yang sudah disahkan. Jangan menganggapnya sebagai larangan untuk perbaikan berikutnya. Anti-slop tidak berarti menghilangkan karakter ceria dan menjadikan semua halaman seragam.

## Pemeriksaan praktis setiap perubahan antarmuka

1. Tetapkan tujuan halaman, pengguna, tindakan utama, dan hierarki informasi sebelum mengubah tampilannya. Nyatakan ENERGY/RHYTHM/MOTION sesuai konteks halaman, bukan satu nilai untuk seluruh aplikasi.
2. Catat alasan singkat untuk pilihan warna, tipografi, jarak, kartu, ikon, ilustrasi, dan gerakan yang berarti. Gunakan elemen visual untuk membantu pemahaman, bukan mengisi ruang.
3. Pakai bahasa Indonesia sederhana dan label tindakan spesifik, misalnya “Simpan jurnal”, “Periksa karya”, atau “Unduh laporan”. Hindari slogan dan klaim tanpa bukti.
4. Jangan membuat statistik, prestasi, testimoni, identitas sekolah, atau dokumentasi yang tampak nyata tanpa sumber. Bedakan ilustrasi, data contoh, dan dokumentasi sebenarnya.
5. Pastikan tombol, tautan, formulir, navigasi, dan slider memiliki fungsi nyata. Fitur belum aktif harus dijelaskan dengan jujur; jangan menampilkan kontrol yang menyesatkan.
6. Periksa keadaan kosong, sedang diproses jika ada proses asinkron, gagal, validasi, dan berhasil. Pertahankan input pengguna saat terjadi kesalahan yang dapat diperbaiki.
7. Periksa ponsel, teks panjang, fokus keyboard, label formulir, dialog, kontras, dan ukuran sasaran sentuh. Status tidak disampaikan melalui warna saja.
8. Gunakan gerakan seperlunya; hindari perpindahan otomatis yang mengganggu membaca atau memutar video. Pertimbangkan preferensi pengurangan gerakan.
9. Edit sumber pada komponen yang tepat, gunakan kembali komponen yang sesuai, dan hindari lapisan CSS atau abstraksi baru yang tidak diperlukan.
10. Jalankan aplikasi dan uji interaksi yang terpengaruh. Laporkan bukti sesuai cakupan; jangan mengklaim seluruh aplikasi telah diaudit dari pemeriksaan satu halaman.

## Pelaporan kualitas

Gunakan empat kelompok Delivery Gate upstream untuk pekerjaan UI: Hard Gate, Purpose-Gate, Liveliness, dan Craftsmanship/Quality Locks. Catat hasil beserta bukti dan cakupan pemeriksaannya. Pemeriksaan yang belum dilakukan ditandai belum diverifikasi, bukan PASS. Kegagalan harus diperbaiki atau dinyatakan sebagai keterbatasan yang masih tersisa; jangan menyembunyikannya demi laporan lulus.

Perubahan dokumentasi seperti adopsi ini tidak membuktikan UI sudah memenuhi Delivery Gate. Pemeriksaan dijalankan pada implementasi fitur atau audit antarmuka berikutnya. Dark mode, ikon baru, dan aset baru tidak otomatis menjadi fitur wajib hanya karena muncul pada rujukan; evaluasi berdasarkan kebutuhan dan cakupan yang disepakati.

## Pembaruan sumber

Salinan upstream dipatok pada revisi di atas agar panduan tidak berubah diam-diam. Jika diperbarui, tinjau perbedaannya, pertahankan lisensi, catat revisi baru, dan perbarui adaptasi proyek bila diperlukan.
