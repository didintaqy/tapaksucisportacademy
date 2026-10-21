# Panduan Deployment

Dokumen ini menjelaskan langkah deploy, konfigurasi produksi, dan strategi backup.

> Untuk deploy otomatis ke **Hostinger Premium** via GitHub Actions + SSH, lihat
> **`docs/DEPLOYMENT-HOSTINGER.md`**.

## Kebutuhan Server

- PHP 8.2 dengan ekstensi: `mbstring`, `pdo`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `dom`, `simplexml`, `gd`, `zip`, `fileinfo`, dan `pdo_mysql`/`pdo_sqlite`
  - `gd`, `dom`, `zip`, dan `simplexml` dibutuhkan untuk export Excel (PhpSpreadsheet) dan PDF (DomPDF).
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
LOG_STACK=daily
LOG_LEVEL=warning
LOG_DAILY_DAYS=30
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

Email notifikasi (kontak, pendaftaran, dan pengingat tagihan) dikirim lewat queue. Jalankan worker lewat Supervisor (VPS):

> Untuk shared hosting Hostinger tanpa Supervisor, gunakan cron queue seperti di
> `docs/DEPLOYMENT-HOSTINGER.md`.

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

Contoh konfigurasi Supervisor (`/etc/supervisor/conf.d/tapak-suci-worker.conf`):

```ini
[program:tapak-suci-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/ke/project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/ke/project/storage/logs/worker.log
stopwaitsecs=3600
```

Tambahkan cron untuk scheduler (wajib agar pengingat tagihan berjalan):

```cron
* * * * * cd /path/ke/project && php artisan schedule:run >> /dev/null 2>&1
```

Setiap selesai deploy, restart worker agar memuat kode terbaru:

```bash
php artisan queue:restart
```

Jadwal yang terdaftar:

| Waktu | Perintah | Fungsi |
|---|---|---|
| 08:00 setiap hari | `payments:send-reminders --days=3` | Kirim email pengingat tagihan jatuh tempo ≤ 3 hari |
| 03:00 setiap hari | `queue:prune-failed --hours=168` | Hapus failed job lebih tua dari 7 hari |

### Monitoring queue

```bash
php artisan queue:monitor default --max=100
php artisan queue:failed
php artisan queue:retry all
```

Jika jumlah job menunggu melebihi 100 atau `failed_jobs` terus bertambah, periksa
worker Supervisor, kredensial SMTP, dan log worker. Detail lengkap ada di
`docs/MONITORING.md`.

### Perintah Keuangan Manual

```bash
# Pratinjau tagihan yang akan diingatkan (tanpa mengirim)
php artisan payments:send-reminders --days=3 --dry-run

# Kirim pengingat sekarang
php artisan payments:send-reminders --days=7

# Periksa selisih status gateway tanpa mengubah data
php artisan payments:reconcile-gateway

# Terapkan penyelarasan status dari payload gateway
php artisan payments:reconcile-gateway --apply
```

Semua perintah di atas aman dijalankan berulang: reminder harian tidak dikirim dua kali untuk tagihan yang sama pada hari yang sama, dan rekonsiliasi hanya mengubah baris yang tidak sinkron.

## Pemeriksaan Pasca-Deploy

```bash
php artisan about
php artisan schedule:list
php artisan queue:work --once   # pastikan worker bisa memproses satu job
php artisan queue:monitor default --max=100
curl -i https://domain-anda.com/up
```

Buka `/admin/income-report` dan pastikan halaman rekap serta tombol export berfungsi. Uji salah satu export Excel/PDF untuk memastikan ekstensi PHP lengkap.

## Logging & Monitoring

- Produksi memakai `LOG_CHANNEL=stack`, `LOG_STACK=daily`, `LOG_LEVEL=warning`, retensi 30 hari.
- Endpoint health: `GET /up` (HTTP 200).
- Pasang uptime monitor (UptimeRobot/Better Stack) ke `/up` setiap 1–5 menit.
- Pantau `failed_jobs` dan ukuran queue secara berkala.
- Prosedur lengkap: **`docs/MONITORING.md`**.

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
- [ ] Queue worker berjalan (Supervisor) dan scheduler cron aktif
- [ ] `LOG_STACK=daily`, `LOG_LEVEL=warning`, retensi 30 hari
- [ ] Uptime monitor memantau `/up` dan alert aktif
- [ ] `queue:restart` dijalankan setiap deploy
- [ ] `queue:prune-failed --hours=168` terjadwal
- [ ] Backup otomatis berjalan
