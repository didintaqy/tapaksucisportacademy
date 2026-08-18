# Tapak Suci Sport Academy Official Web

Website resmi yang sedang dikembangkan untuk memperkenalkan Tapak Suci Sport Academy sebagai pusat latihan bela diri, pembentukan karakter, dan pengembangan prestasi atlet muda.

## Gambaran Umum

Project ini dibangun dengan Laravel untuk menyediakan landing page dan halaman profil organisasi yang modern, responsif, dan mudah dikembangkan untuk kebutuhan website resmi akademi.

Saat ini website mencakup beberapa halaman utama seperti:

- Halaman utama / home
- Halaman tentang / about
- Halaman program / program

## Tujuan Website

Website ini dibuat untuk:

- Memperkenalkan Tapak Suci Sport Academy secara profesional
- Menampilkan visi, misi, dan profil organisasi
- Menyampaikan program latihan dan kegiatan akademi
- Menjadi media informasi bagi calon siswa, orang tua, dan mitra
- Menjadi fondasi pengembangan website resmi yang lebih lengkap di masa depan

## Fitur

- Desain modern dan responsif
- Halaman Home untuk promosi utama
- Halaman About untuk profil dan nilai organisasi
- Halaman Program untuk menampilkan program latihan
- Struktur Laravel yang siap dikembangkan lebih lanjut
- Integrasi asset frontend dengan Vite

## Teknologi yang Digunakan

- PHP 8.2
- Laravel 12
- Blade Template
- Vite
- Bootstrap / CSS custom

## Struktur Project

- `app/` — logika aplikasi dan controller
- `routes/` — definisi route website
- `resources/views/` — view Blade untuk frontend
- `public/` — aset statis seperti gambar, CSS, dan JS
- `database/` — migration dan seeder
- `config/` — konfigurasi aplikasi

## Cara Menjalankan Project

1. Install dependency PHP

```bash
composer install
```

2. Salin file environment

```bash
cp .env.example .env
```

3. Generate key aplikasi

```bash
php artisan key:generate
```

4. Jalankan migrasi database

```bash
php artisan migrate
```

5. Install dependency frontend

```bash
npm install
```

6. Build asset frontend

```bash
npm run build
```

7. Jalankan server lokal

```bash
php artisan serve
```

Setelah itu, buka browser ke:

```text
http://localhost:8000
```

## Route Utama

- `/` — Home
- `/about` — About Us
- `/program` — Program

## Catatan

Project ini masih dalam tahap pengembangan website resmi. Selanjutnya dapat dikembangkan dengan fitur seperti:

- Form pendaftaran siswa
- Galeri kegiatan
- Testimoni
- Kontak dan lokasi
- Dashboard admin

## Lisensi

Project ini menggunakan lisensi MIT seperti default Laravel project.
