# Panduan Deployment

Dokumen ini menjelaskan langkah deploy, konfigurasi produksi, dan strategi backup.

## Kebutuhan Server

- PHP 8.2 dengan ekstensi: `mbstring`, `pdo`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, dan `pdo_mysql`/`pdo_sqlite`
- Composer 2
- Node.js 20+ (hanya untuk build asset; bisa dilakukan di mesin lokal/CI)
- Web server (Nginx/Apache) mengarah ke folder `public/`
- Database: MySQL/MariaDB (disarankan) atau SQLite

## Konfigurasi Produksi (`.env`)

```dotenv
APP_NAME="Tapak Suci Sport Academy"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tapak_suci
DB_USERNAME=...
DB_PASSWORD=...

PAYMENT_GATEWAY=midtrans
MIDTRANS_SERVER_KEY=...
MIDTRANS_CLIENT_KEY=...
MIDTRANS_IS_PRODUCTION=true

ADMIN_NAME="Admin"
ADMIN_EMAIL=admin@domain-anda.com
ADMIN_PASSWORD=password-kuat

LOG_CHANNEL=stack
```

Jangan pernah commit file `.env`.

## Konfigurasi Analytics (Opsional)

Tambahkan ke `.env` untuk production:

```dotenv
# Google Analytics 4
GA_MEASUREMENT_ID=G-XXXXXXXXXX

# ATAU Umami (self-hosted)
UMAMI_WEBSITE_ID=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
UMAMI_SCRIPT_URL=https://analytics.domain-anda.com/script.js
```

Pilih salah satu. Keduanya hanya di-load saat `APP_ENV=production`.

## Langkah Deploy

```bash
git switch main
git pull origin main

composer install --no-dev --optimize-autoloader

cp .env.example .env   # hanya saat pertama kali
php artisan key:generate

php artisan migrate --force

php artisan storage:link

npm ci
npm run build

php artisan optimize
php artisan filament:optimize
```

Setelah deploy, jalankan seeder hanya bila database baru:

```bash
php artisan db:seed --force
```

## Queue dan Scheduler

Saat fitur notifikasi diaktifkan, jalankan worker lewat Supervisor:

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

Tambahkan cron untuk scheduler:

```cron
* * * * * cd /path/ke/project && php artisan schedule:run >> /dev/null 2>&1
```

## Strategi Backup

### MySQL/MariaDB

```bash
mysqldump --single-transaction --routines --triggers \
  -u USER -p DATABASE | gzip > backup-$(date +%F-%H%M).sql.gz
```

### SQLite

Hentikan penulisan sementara, lalu salin file database:

```bash
sqlite3 database/database.sqlite ".backup 'backup-$(date +%F-%H%M).sqlite'"
```

### Berkas Upload

Backup folder `storage/app/public` (foto siswa, galeri, bukti bayar).

### Aturan

- Simpan backup harian, retensi minimal 14 hari.
- Simpan salinan di lokasi berbeda dari server aplikasi.
- Uji restore minimal sekali sebelum go-live.
- Backup sebelum setiap migrasi besar.

## Checklist Keamanan

- [ ] `APP_DEBUG=false` dan `APP_ENV=production`
- [ ] HTTPS aktif dan redirect dari HTTP
- [ ] Password admin kuat, bukan default
- [ ] Kredensial gateway hanya di `.env`
- [ ] Folder `storage` dan `bootstrap/cache` dapat ditulis, selain itu read-only
- [ ] Akses langsung ke `.env` dan folder `database` diblokir web server
- [ ] Rate limit form publik aktif (kontak: 5 permintaan/menit)
- [ ] Backup otomatis berjalan
