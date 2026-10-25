# Panduan Operator — Panel Admin

Panduan ini untuk **Admin** dan **Operator** akademi yang mengelola isi situs dan
data harian lewat panel admin. Tidak perlu pengetahuan teknis.

- **URL panel:** `https://tapaksucisportacademy.com/admin`
- **Butuh bantuan teknis?** Lihat `docs/MONITORING.md` dan `docs/DEPLOYMENT.md`.
- **Terakhir diperbarui:** 26 Oktober 2026

---

## 1. Masuk dan Peran Pengguna

| Peran | Bisa apa | Catatan |
|---|---|---|
| **Administrator** | Semua menu, termasuk Pengguna & Akses | Satu-satunya yang bisa mengelola pengguna dan menerbitkan artikel |
| **Operator** | Mengelola data harian dan konten | Tidak bisa menghapus kategori/tag/newsletter, tidak bisa menerbitkan artikel |
| **Pelatih** | Absensi dan UKT sesuai jadwal yang ia ampu | Tidak bisa menghapus data; tidak melihat keuangan |

**Masuk:** buka `/admin`, masukkan email dan kata sandi. Setelah 5 percobaan
gagal, login dikunci sementara (1 menit) untuk keamanan.

**Jika lupa kata sandi:** hubungi Administrator. Administrator menggantinya di
**Pengguna & Akses → Manajemen Pengguna**.

> Jangan memakai kata sandi yang sama dengan akun pribadi. Gunakan kata sandi
> minimal 12 karakter.

---

## 2. Peta Menu

| Grup | Menu | Untuk apa |
|---|---|---|
| **Master Data** | Daftar Siswa | Data induk siswa |
| | Performa Siswa | Catatan fisik/teknik siswa |
| **Manajemen Akademi** | Absensi | Kehadiran per sesi latihan |
| | Sabuk / UKT | Riwayat sabuk dan ujian kenaikan tingkat |
| **Keuangan** | Jenis Pembayaran | Master SPP, pendaftaran, UKT, event |
| | Pembayaran Siswa | Tagihan dan pembayaran |
| | Rekap Pemasukan | Laporan pemasukan per periode |
| | Laporan Tunggakan | Siswa yang belum membayar |
| | Rekonsiliasi Gateway | Cocokkan status bayar dengan gateway |
| | Rapor Performa | Cetak rapor PDF |
| **Manajemen Web** | Galeri Kegiatan | Foto kegiatan |
| | Blog & Artikel | Tulisan |
| | Kategori / Tag | Klasifikasi artikel |
| | Program Latihan | Program, biaya, cabang |
| | Jadwal Latihan | Slot latihan per cabang |
| | Pendaftaran Siswa | Pendaftar dari situs |
| | Kalender Kegiatan | Agenda dan pengumuman |
| | Testimoni | Testimoni publik |
| | FAQ | Pertanyaan umum |
| | Kontak Masuk | Pesan dari form kontak |
| | Newsletter / Broadcast | Kirim email massal |
| | Pelatih | Profil pelatih publik |
| | Pengaturan Situs | Identitas, kontak, peta, hero, SEO, footer |
| **Pengguna & Akses** | Manajemen Pengguna | Akun admin/operator/pelatih (Admin saja) |

---

## 3. Rutinitas Harian

### 3.1 Pagi — periksa masuk

1. **Manajemen Web → Kontak Masuk** — pesan baru bertanda belum dibaca.
   Tandai **Dibaca** atau **Dibalas** setelah ditindaklanjuti.
2. **Manajemen Web → Pendaftaran Siswa** — calon siswa dari situs.
   Tindak lanjuti, lalu buat data siswa di **Master Data → Daftar Siswa**.
3. **Keuangan → Pembayaran Siswa** — tagihan baru dan status pembayaran.

> Pendaftaran online **tidak otomatis** menjadi siswa. Data siswa dibuat manual
> karena NIK dan data lain belum lengkap saat mendaftar.

### 3.2 Absensi

1. Buka **Manajemen Akademi → Absensi**.
2. Pilih jadwal latihan dan tanggal, lalu tandai kehadiran.
3. Pelatih hanya melihat jadwal yang ditugaskan kepadanya.

### 3.3 Menangani pembayaran

1. **Keuangan → Pembayaran Siswa**, cari siswa.
2. Buka tagihan, lihat status. Pembayaran QRIS masuk otomatis lewat gateway.
3. Untuk pembayaran **tunai**, ubah status dan isi alasan — setiap perubahan
   tercatat di riwayat beserta nama pengubah.
4. Cek **Rekonsiliasi Gateway** bila status terasa tidak sinkron dengan gateway.

**Penting:** jangan mengubah status pembayaran tanpa alasan tertulis. Riwayat
perubahan adalah bagian dari audit keuangan.

---

## 4. Mengubah Konten Situs

### 4.1 Pengaturan Situs (paling sering dipakai)

**Manajemen Web → Pengaturan Situs**. Semua tab di sini langsung mengubah situs
publik tanpa deploy:

| Tab | Isi |
|---|---|
| Identitas & logo | Nama, nama pendek, tagline, logo, favicon, latar header |
| Kontak & lokasi | Alamat, telepon, WhatsApp, email, jam, link + embed Google Maps |
| Media sosial | Facebook, Instagram, YouTube, TikTok, X |
| Beranda | Hero, section, langkah, keunggulan, gambar latar section |
| Tentang kami | Sejarah, visi, misi, nilai |
| Program & biaya | Iuran bulanan, biaya pendaftaran, catatan biaya |
| SEO | Judul dan deskripsi default untuk mesin pencari |
| Footer | Deskripsi, tautan footer, gambar latar footer |

**Aturan penting:** bagian yang kosong **tidak ditampilkan** di situs. Ini
disengaja agar tidak ada konten contoh. Jika section hilang dari situs, biasanya
datanya kosong.

**Embed Google Maps:** tempel seluruh kode `<iframe>` dari Google Maps, atau URL
`https://www.google.com/maps/embed?...`. Tautan `maps.app.goo.gl` saja **tidak**
bisa ditampilkan sebagai peta.

### 4.1.1 Mengganti gambar

Setiap gambar punya kolom unggah sendiri. Setelah memilih berkas, tekan
**Simpan** — gambar lama otomatis dipindahkan ke karantina, bukan langsung
dihapus, sehingga masih bisa dipulihkan.

Memulihkan gambar yang salah ganti (khusus teknis):

```bash
php artisan site-settings:prune-media --list                    # lihat isi karantina
php artisan site-settings:prune-media --restore=site/logo.png   # kembalikan berkas
```

Setelah dipulihkan, unggah ulang berkas tersebut di kolom yang benar, lalu
Simpan. Karantina dibersihkan permanen dengan `--empty-trash`.

**Batas simpan:** gambar di karantina hanya bertahan **30 hari**, setelah itu
dihapus permanen otomatis. Batas ini diatur `SITE_MEDIA_TRASH_RETENTION_DAYS`
di `.env` (isi `0` untuk menyimpan selamanya). Pastikan gambar yang salah
ganti dipulihkan sebelum masa itu lewat.

### 4.2 Menambah artikel

1. **Manajemen Web → Blog & Artikel → Buat**.
2. Isi judul, konten, kategori, dan tag. Unggah gambar utama.
3. Simpan sebagai draf, lalu ajukan review.
4. **Hanya Administrator** yang bisa menekan **Terbitkan**.

### 4.3 Newsletter

1. **Manajemen Web → Newsletter / Broadcast → Buat**.
2. Isi subjek dan konten.
3. Aksi **Kirim** memerlukan izin khusus (Admin/Operator). Email dikirim lewat
   antrean, bukan langsung — beri waktu beberapa menit.

> Kirim percobaan ke email sendiri sebelum mengirim ke semua penerima.

---

## 5. Yang TIDAK Boleh Dilakukan

| Jangan | Alasan |
|---|---|
| Menghapus siswa yang sudah punya pembayaran | Riwayat keuangan menjadi tidak lengkap. Gunakan status nonaktif. |
| Mengubah nominal tagihan yang sudah dibayar | Merusak audit. Buat koreksi baru dan catat alasannya. |
| Membagikan akun admin | Setiap perubahan tercatat dengan nama pengguna. |
| Mengunggah foto siswa tanpa izin orang tua | Lihat kebijakan privasi di `docs/MILESTONE-DATA-REAL.md` §5. |
| Menempelkan skrip/HTML sembarang di pengaturan | Bisa merusak tampilan atau keamanan situs. |

---

## 6. Privasi Data Siswa

Halaman publik **hanya** boleh menampilkan jumlah agregat siswa, profil pelatih,
dan testimoni yang disetujui. **Jangan** menampilkan NIK, nomor KK, alamat rumah,
nomor telepon, email, atau foto siswa di konten publik.

Portal pembayaran meminta **ID siswa/NIK + tanggal lahir**. Jika orang tua
melaporkan tidak bisa masuk, periksa apakah tanggal lahir siswa sudah terisi di
**Master Data → Daftar Siswa**.

---

## 7. Jika Ada Masalah

| Gejala | Yang dicek |
|---|---|
| Halaman situs kosong pada satu section | Data di Pengaturan Situs mungkin kosong |
| Peta tidak muncul | Embed Google Maps belum valid (lihat §4.1) |
| Email tidak terkirim | Antrean mungkin tertahan; hubungi teknis untuk cek antrean |
| Pembayaran tidak masuk | Cek Rekonsiliasi Gateway, lalu hubungi teknis |
| Tidak bisa login | Coba lagi setelah 1 menit; jika tetap gagal hubungi Administrator |

Untuk gangguan yang memengaruhi semua pengguna (situs tidak bisa dibuka, data
hilang), **hentikan perubahan** dan hubungi penanggung jawab teknis segera.

---

## 8. Checklist Pelatihan Operator

Cetak dan centang saat sesi pelatihan:

- [ ] Bisa login dan keluar, mengganti kata sandi
- [ ] Mengenal seluruh menu dan grup sidebar
- [ ] Membaca dan menindaklanjuti Kontak Masuk
- [ ] Memproses satu Pendaftaran Siswa menjadi data siswa
- [ ] Mencatat absensi untuk satu sesi latihan
- [ ] Mengubah status satu pembayaran **beserta alasannya**
- [ ] Mengubah kontak di Pengaturan Situs lalu memverifikasi di situs publik
- [ ] Membuat satu artikel, menyimpan draf, mengajukan review
- [ ] Menambah satu foto galeri
- [ ] Menjelaskan aturan privasi data siswa (§6)
- [ ] Tahu siapa yang dihubungi saat ada gangguan
