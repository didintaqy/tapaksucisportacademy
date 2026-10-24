# Milestone — Data Nyata & Pengaturan Situs

- **Status:** implementasi teknis selesai dan diuji (209 test / 808 asersi lulus, `vendor/bin/pint` lulus); menunggu data resmi dari pengelola akademi
- **Tanggal:** 25 Oktober 2026
- **Branch:** `main` (dikerjakan langsung di working tree)
- **Acuan:** `docs/ROADMAP.md`, `docs/TODO.md`

---

## 1. Tujuan

Mengganti seluruh konten statis template Blade dengan data yang dapat dikelola dari panel admin. Setelah milestone ini:

1. Identitas, kontak, jam operasional, alamat, peta, dan media sosial situs dapat diubah tanpa mengedit kode.
2. Program, iuran bulanan, jadwal, pelatih, testimoni, FAQ, blog, galeri, dan kalender tampil dari database.
3. Bagian yang belum memiliki data nyata otomatis disembunyikan, bukan menampilkan konten contoh.
4. Tidak ada data pribadi siswa (NIK, alamat, nomor telepon, email) yang tampil di halaman publik.
5. Pengelola dapat melihat dengan jelas data mana yang masih berupa placeholder.

---

## 2. Keputusan Implementasi

| Topik | Keputusan | Alasan |
|---|---|---|
| Konfigurasi situs | Tabel `site_settings` satu baris dengan kolom JSON `data` | Fleksibel, cukup satu halaman admin, mudah ditambah field |
| Service | `App\Services\SiteSettings` | Default terpusat, cache, konversi URL media, validasi peta |
| Pengelolaan | Halaman Filament **Pengaturan Situs** (`/admin/site-settings`) | Satu tempat untuk identitas, kontak, hero, about, biaya, SEO, footer |
| Profil pelatih | Tabel `coaches` + resource Filament | Data tim harus dapat diedit, bukan nama contoh di Blade |
| Lokasi | Satu lokasi pusat | Sesuai keputusan bisnis saat ini; multi-cabang menyusul bila diperlukan |
| Peta | URL/embed Google Maps dari pengaturan | Tidak ada lagi koordinat atau alamat Jakarta palsu |
| Testimoni/FAQ/blog/galeri/kalender | Ambil dari database, sembunyikan jika kosong | Tidak menampilkan konten demo di produksi |
| Statistik & rating | Dihitung dari database | Menghapus angka palsu `500+`, `4.9`, dan `4200 review` |
| Konten marketing | Repeater di pengaturan situs | Tetap dapat diubah tanpa deploy |
| Data produksi | `ProductionSeeder` hanya membuat admin, jenis pembayaran, dan pengaturan awal | Tidak menyalin siswa/konten demo |

---

## 3. Cakupan Perubahan

### 3.1 Database & Model

- [x] Migrasi `site_settings` (`data` JSON).
- [x] Model `SiteSetting` dengan cast array dan pembersihan cache.
- [x] Migrasi `coaches` (nama, jabatan, keahlian, bio, foto, pengalaman, urutan, status aktif).
- [x] Model `Coach` dengan scope `active()`/`ordered()`, URL foto, dan inisial.
- [x] Factory `CoachFactory` untuk pengujian.

### 3.2 Pengaturan Situs

- [x] Service `SiteSettings`: default, baca/tulis, merge default, cache, URL media, URL WhatsApp, ekstraksi embed Google Maps yang aman.
- [x] `SiteSettingSeeder` idempotent.
- [x] `ProductionSeeder` memanggil seeder pengaturan.
- [x] Halaman Filament **Pengaturan Situs** dengan tab:
  - Identitas & logo
  - Kontak & lokasi/peta
  - Media sosial
  - Beranda (hero, section, langkah, keunggulan)
  - Tentang kami (sejarah, visi, misi, nilai)
  - Program & biaya
  - SEO
  - Footer
- [x] Hanya Admin/Operator yang dapat membuka pengaturan.

### 3.3 Profil Pelatih

- [x] Resource Filament **Pelatih** pada grup **Manajemen Web**.
- [x] Form: nama, jabatan, keahlian, bio, foto, pengalaman, urutan, status aktif.
- [x] Tabel: foto/inisial, nama, jabatan, keahlian, status, urutan.
- [x] `CoachPolicy`: Admin/Operator lihat & ubah; hapus hanya Admin.
- [x] Section tim di beranda dan tentang kami hanya muncul jika ada pelatih aktif.

### 3.4 Frontend Dinamis

- [x] `layout/master.blade.php`: title, meta description, keywords, Open Graph, Twitter Card, favicon, canonical, analytics.
- [x] `layout/header.blade.php`: logo dari pengaturan, menu tanpa tautan `.html` mati.
- [x] `layout/footer.blade.php`: deskripsi, logo, sosial, jam, kontak, copyright dinamis.
- [x] `layout/herosection/video.blade.php`: hero dari pengaturan, tanpa video demo.
- [x] `layout/herosection/page-header.blade.php`: breadcrumb dan latar dinamis.
- [x] Beranda: about, program, keunggulan, langkah, pelatih, fitur, biaya, testimoni, FAQ, CTA, blog.
- [x] Tentang kami: profil, visi/misi, nilai, statistik, pelatih, testimoni, FAQ.
- [x] Program: daftar program, iuran, langkah pendaftaran, testimoni, FAQ, CTA.
- [x] Detail program: biaya pendaftaran, iuran, jadwal, CTA.
- [x] Kontak: kontak dinamis + peta hanya jika diisi.
- [x] Pendaftaran: kontak dan biaya dari pengaturan.
- [x] FAQ: kontak sidebar dinamis.
- [x] Jadwal: CTA kontak dinamis.
- [x] Testimoni: inisial nama, bukan foto contoh.
- [x] Blog: kartu artikel dari database; share artikel memakai URL asli.
- [x] Galeri: perbaikan URL gambar.
- [x] Kalender: CTA tetap, konten dari database.

### 3.5 SEO

- [x] SEO default dari pengaturan situs.
- [x] Composer tidak lagi menimpa `seoTitle`, `seoDescription`, atau `seoImage` yang diberikan controller.
- [x] Judul halaman detail program/artikel tetap memakai data record.
- [x] OG image default memakai aset yang benar-benar ada.

### 3.6 Pengujian

- [x] Test service pengaturan (default, simpan, cache, URL peta/WhatsApp/media).
- [x] Test akses halaman pengaturan dan policy pelatih.
- [x] Test konten dinamis beranda/kontak/tentang/pendaftaran.
- [x] Test bagian kosong tidak dirender.
- [x] Test SEO controller tidak ditimpa.
- [x] `PolicyCoverageTest` mencakup `Coach`.
- [x] `php artisan test` dan `vendor/bin/pint` lulus.

### Verifikasi terakhir (25 Oktober 2026)

- `php artisan test --compact`: **209 test, 808 asersi, 0 kegagalan**.
- `vendor/bin/pint --dirty`: **lulus**.
- Smoke test HTTP `/`, `/about`, `/program`, `/pendaftaran`, `/jadwal`, `/kalender`, `/gallery`, `/blog`, `/testimoni`, `/faq`, `/contact-us`, `/sitemap.xml`, detail program, detail blog: semua **200 OK**.
- Halaman admin `/admin/site-settings` dan `/admin/coaches`: redirect ke login (302) untuk tamu; akses Admin/Operator diuji via feature test.
- Tidak ada error runtime di `storage/logs/laravel.log` saat smoke test.

---

## 4. Data yang Masih Harus Diisi Pengelola

Semua field berikut tersedia di **Admin → Pengaturan Situs** dan **Admin → Manajemen Web → Pelatih**.

### 4.1 Identitas

- [ ] Nama resmi akademi dan nama pendek
- [ ] Tagline
- [ ] Logo (disarankan PNG/SVG transparan)
- [ ] Favicon
- [ ] Foto latar header halaman
- [ ] Foto hero beranda

### 4.2 Kontak & Lokasi

- [ ] Alamat lengkap lokasi latihan pusat
- [ ] Nomor telepon
- [ ] Nomor WhatsApp (format bebas, sistem menormalkan ke `wa.me`)
- [ ] Email resmi
- [ ] Jam operasional
- [ ] Link Google Maps (`https://maps.app.goo.gl/...` atau link tempat)
- [ ] Kode embed Google Maps (tempel seluruh `<iframe>` atau URL `https://www.google.com/maps/embed?...`)

### 4.3 Media Sosial

- [ ] Facebook
- [ ] Instagram
- [ ] YouTube
- [ ] TikTok
- [ ] X/Twitter

Kosongkan yang tidak digunakan; ikon tidak akan tampil.

### 4.4 Program & Biaya

- [ ] Verifikasi nama, deskripsi, usia, durasi, dan cabang setiap program
- [ ] Iuran bulanan setiap program
- [ ] Biaya pendaftaran
- [ ] Catatan biaya (misalnya seragam, ujian, atau biaya lain)
- [ ] Program unggulan untuk beranda

### 4.5 Pelatih

- [ ] Nama pelatih
- [ ] Jabatan (mis. Pelatih Kepala, Asisten Pelatih)
- [ ] Keahlian
- [ ] Pengalaman
- [ ] Bio singkat
- [ ] Foto (opsional; jika kosong tampil inisial)

### 4.6 Konten Publik

- [ ] Testimoni nyata (disetujui admin)
- [ ] FAQ resmi
- [ ] Artikel blog
- [ ] Foto galeri kegiatan
- [ ] Agenda/kegiatan di kalender

**Catatan:** selama data ini kosong, section terkait tidak ditampilkan. Ini disengaja agar situs produksi tidak berisi konten contoh.

---

## 5. Kebijakan Privasi Data

1. Halaman publik hanya boleh menampilkan:
   - jumlah agregat siswa (tanpa identitas),
   - nama pelatih yang memang tampil sebagai profil publik,
   - testimoni yang sudah disetujui dan dikirim untuk publikasi.
2. Dilarang menampilkan NIK, nomor kartu keluarga, alamat rumah, nomor telepon siswa, email siswa, atau foto siswa tanpa persetujuan.
3. Data siswa tetap hanya dapat diakses Admin/Operator/Pelatih sesuai policy yang berlaku.
4. Statistik publik dihitung dari database dan tidak menyertakan data pribadi.

---

## 6. Definisi Selesai

1. Tidak ada lagi alamat, telepon, email, harga, nama pelatih, rating, atau tautan sosial contoh di Blade.
2. Semua konten publik berasal dari database atau pengaturan situs.
3. Bagian tanpa data tidak tampil dan tidak menghasilkan error.
4. Admin/Operator dapat mengubah identitas, kontak, peta, sosial, hero, about, biaya, SEO, dan footer tanpa deploy.
5. Admin/Operator dapat mengelola profil pelatih; Pelatih tidak dapat mengakses pengaturan situs.
6. Peta hanya dirender jika URL embed valid milik Google Maps.
7. Seluruh test dan `pint` lulus.
8. `docs/ROADMAP.md` dan `docs/TODO.md` diperbarui.

---

## 7. Rollout Produksi

1. Deploy kode dan jalankan `php artisan migrate --force`.
2. Jalankan `php artisan db:seed --class=ProductionSeeder --force` **hanya pada instalasi baru**.
3. Login sebagai Admin, buka **Pengaturan Situs**, lalu isi checklist bagian 4.
4. Tambah pelatih melalui menu **Pelatih**.
5. Verifikasi halaman publik: beranda, tentang, program, kontak (peta), pendaftaran, FAQ.
6. Pastikan `APP_URL` dan `ASSET_URL` benar agar logo, favicon, dan foto tampil.
7. Jalankan `php artisan optimize:clear` setelah perubahan konfigurasi.
8. Uji kirim pesan kontak dan pendaftaran untuk memastikan email tujuan benar.

---

## 8. Batasan & Tindak Lanjut

- Satu lokasi pusat; multi-lokasi memerlukan model `locations` dan relasi ke jadwal.
- Foto section marketing memakai aset tema sampai foto resmi diunggah.
- Testimoni belum memiliki kolom foto; avatar memakai inisial. Kolom foto dapat ditambahkan bila diperlukan.
- Belum ada halaman detail pelatih; kartu pelatih hanya menampilkan profil singkat.
- Biaya pendaftaran bersifat informasi; belum otomatis membuat tagihan saat pendaftaran diterima.
- Peta membutuhkan URL embed Google Maps; tautan `maps.app.goo.gl` saja tidak dapat ditanam sebagai iframe.
- Setelah data resmi diisi, lakukan UAT visual di mobile (360px/390px) dan desktop.
