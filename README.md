# Tapak Suci Sport Academy Official Web

Website resmi Tapak Suci Sport Academy: profil akademi, konten kegiatan, dan portal pembayaran siswa. Aplikasi dibangun dengan Laravel dan panel admin Filament.

## Fitur Saat Ini

### Website publik

- Home, About, dan Program
- Galeri kegiatan
- Blog & artikel (index dan detail)
- Testimoni dengan moderasi admin
- FAQ per kategori
- Jadwal latihan per cabang dan kelas
- Pendaftaran siswa online
- Kontak: form tersimpan ke database, info kontak, dan peta
- Portal pembayaran siswa: cari berdasarkan ID/NIK, lihat tagihan, checkout QRIS, dan cetak kwitansi

### Panel admin (`/admin`)

- **Master Data:** Siswa, Performa Siswa
- **Keuangan:** Pembayaran, Jenis Pembayaran, generate tagihan bulanan
- **Manajemen Web:** Galeri, Blog & Artikel, Pesan Kontak, Testimoni, FAQ, Jadwal Latihan, Pendaftaran Siswa
- **Pengguna:** manajemen akun admin (khusus role Administrator)
- **Dashboard:** ringkasan angka, grafik pemasukan 6 bulan, tagihan yang perlu tindakan, dan ringkasan performa fisik

### Role pengguna

| Role | Akses |
|---|---|
| Administrator | Semua modul, termasuk manajemen pengguna dan penghapusan data keuangan |
| Operator | Siswa, pembayaran, konten, pesan kontak, testimoni, FAQ, jadwal, dan pendaftaran; tanpa manajemen pengguna |
| Pelatih | Melihat siswa dan mengelola data performa fisik; tanpa akses keuangan dan konten |

Role disimpan di kolom `users.role` dan diatur lewat Policy Laravel.

### Pembayaran

- Gateway `manual` (default), dengan konfigurasi siap untuk Midtrans dan Xendit
- Callback/webhook gateway
- Generate tagihan bulanan massal untuk semua siswa per periode

## Teknologi

- PHP 8.2
- Laravel 12
- Filament 5
- Blade + Bootstrap
- Vite
- SQLite (development)

## Cara Menjalankan Project

1. Install dependency PHP

```bash
composer install
```

2. Salin file environment dan sesuaikan

```bash
cp .env.example .env
```

3. Generate key aplikasi

```bash
php artisan key:generate
```

4. Jalankan migrasi dan seeder

```bash
php artisan migrate --seed
```

5. Install dependency frontend dan build asset

```bash
npm install
npm run build
```

6. Buat symlink storage (untuk upload gambar)

```bash
php artisan storage:link
```

7. Jalankan server lokal

```bash
php artisan serve
```

Buka `http://localhost:8000` untuk website dan `http://localhost:8000/admin` untuk panel admin.

## Route Utama

### Publik

- `/` — Home
- `/about` — About
- `/program` — Program
- `/gallery` — Galeri
- `/blog` — Blog
- `/blog/{slug}` — Detail artikel
- `/testimoni` — Testimoni (GET dan POST)
- `/faq` — FAQ
- `/jadwal` — Jadwal latihan
- `/pendaftaran` — Pendaftaran siswa (GET dan POST)
- `/contact-us` — Kontak (GET dan POST)

### Portal pembayaran

- `/pembayaran` — Cari siswa
- `/pembayaran/tagihan` — Daftar tagihan
- `/pembayaran/checkout/{payment}` — Checkout
- `/pembayaran/kwitansi/{payment}` — Kwitansi

### Admin

- `/admin` — Dashboard Filament
- `/kwitansi/{payment}` — Kwitansi (perlu login)

## Testing

```bash
php artisan test
```

## Dokumentasi

- [Roadmap pengembangan](docs/ROADMAP.md) — rencana dari fondasi sampai go-live
- [Panduan pengembangan](docs/ARCHITECTURE.md) — struktur modul dan alur utama
- [Panduan deployment](docs/DEPLOYMENT.md) — konfigurasi produksi dan backup

## Lisensi

Project ini menggunakan lisensi MIT seperti default Laravel project.
