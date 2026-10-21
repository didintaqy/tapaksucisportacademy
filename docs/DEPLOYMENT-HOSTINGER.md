# Deploy Otomatis ke Hostinger (Premium)

Panduan ini untuk deploy produksi ke **Hostinger Premium** dengan **SSH** dan
**GitHub Actions**. Workflow hanya berjalan saat ada push ke `main` atau saat
dijalankan manual dari tab Actions.

## 1. Arsitektur direktori

Agar file Laravel tidak terekspos ke publik, aplikasi tidak ditaruh langsung di
`public_html`.

```text
/home/u123456789/domains/tapaksucisportacademy.com/
├── laravel/
│   ├── current -> releases/<commit>
│   ├── releases/
│   │   ├── <commit>/
│   │   └── ...
│   └── shared/
│       ├── .env
│       └── storage/
│           ├── app/public/
│           ├── framework/cache/
│           ├── framework/sessions/
│           ├── framework/views/
│           └── logs/
└── public_html/
    ├── index.php          # dibuat otomatis oleh workflow
    ├── .htaccess
    ├── assets/
    ├── css/
    ├── js/
    └── storage -> ../laravel/shared/storage/app/public
```

- `public_html` hanya berisi file publik.
- `.env` dan `storage` disimpan di `shared/` agar tidak hilang saat deploy.
- Workflow menyimpan 3 release terakhir untuk rollback cepat.

## 2. Prasyarat di Hostinger

1. Aktifkan **SSH Access** di `hPanel → Advanced → SSH Access`.
2. Tambahkan **public key** dari SSH key pair yang akan dipakai GitHub Actions.
3. Set **PHP 8.2 atau 8.3** untuk domain `tapaksucisportacademy.com`.
4. Aktifkan ekstensi: `mbstring`, `pdo_mysql`, `openssl`, `xml`, `ctype`,
   `json`, `bcmath`, `fileinfo`, `gd`, `zip`, `intl`.
5. Buat database MySQL + user database di hPanel.
6. Aktifkan SSL/HTTPS untuk domain.
7. Pastikan cron job tersedia di `hPanel → Advanced → Cron Jobs`.

> Port SSH Hostinger umumnya **65002**. Konfirmasi host, port, dan username di
> halaman SSH Access.

## 3. GitHub Secrets

Buka **GitHub → Settings → Environments → New environment → `production`**,
lalu tambahkan secrets berikut di environment tersebut:

| Secret | Contoh | Keterangan |
|---|---|---|
| `SSH_HOST` | `ssh.tapaksucisportacademy.com` atau IP server | Host SSH dari hPanel |
| `SSH_PORT` | `65002` | Port SSH Hostinger |
| `SSH_USER` | `u123456789` | Username SSH |
| `SSH_PRIVATE_KEY` | isi private key | Tanpa passphrase |
| `DEPLOY_PATH` | `/home/u123456789/domains/tapaksucisportacademy.com/laravel` | Root aplikasi |
| `PUBLIC_PATH` | `/home/u123456789/domains/tapaksucisportacademy.com/public_html` | Document root |
| `PHP_BIN` | `/usr/bin/php8.2` atau `php` | Opsional |

Jangan simpan private key atau password di repository.

## 4. Setup awal di server

Login ke SSH:

```bash
ssh -p 65002 u123456789@ssh.tapaksucisportacademy.com
```

Buat struktur direktori:

```bash
mkdir -p ~/domains/tapaksucisportacademy.com/laravel/releases
mkdir -p ~/domains/tapaksucisportacademy.com/laravel/shared/storage/app/public
mkdir -p ~/domains/tapaksucisportacademy.com/laravel/shared/storage/framework/cache/data
mkdir -p ~/domains/tapaksucisportacademy.com/laravel/shared/storage/framework/sessions
mkdir -p ~/domains/tapaksucisportacademy.com/laravel/shared/storage/framework/views
mkdir -p ~/domains/tapaksucisportacademy.com/laravel/shared/storage/logs
mkdir -p ~/domains/tapaksucisportacademy.com/public_html
```

Buat file `.env` produksi:

```bash
nano ~/domains/tapaksucisportacademy.com/laravel/shared/.env
```

Isi minimal:

```dotenv
APP_NAME="Tapak Suci Sport Academy"
APP_ENV=production
APP_KEY=base64:ISI_HASIL_KEY_GENERATE
APP_DEBUG=false
APP_URL=https://tapaksucisportacademy.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_tapaksuci
DB_USERNAME=u123456789_tapaksuci
DB_PASSWORD=PASSWORD_DATABASE

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=email@tapaksucisportacademy.com
MAIL_PASSWORD=PASSWORD_EMAIL
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=email@tapaksucisportacademy.com
MAIL_FROM_NAME="Tapak Suci Sport Academy"

ADMIN_NAME="Administrator"
ADMIN_EMAIL=admin@tapaksucisportacademy.com
ADMIN_PASSWORD=PASSWORD_ADMIN_KUAT

PAYMENT_GATEWAY=midtrans
MIDTRANS_SERVER_KEY=ISI_SERVER_KEY
MIDTRANS_CLIENT_KEY=ISI_CLIENT_KEY
MIDTRANS_IS_PRODUCTION=true

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=warning
LOG_DAILY_DAYS=30
```

Generate `APP_KEY` dari mesin lokal:

```bash
php artisan key:generate --show
```

Salin hasilnya ke `APP_KEY` di `.env` server.

Amankan file `.env`:

```bash
chmod 600 ~/domains/tapaksucisportacademy.com/laravel/shared/.env
```

## 5. Deploy pertama

1. Merge perubahan ke `main`.
2. Buka **GitHub → Actions → Deploy Production**.
3. Workflow akan:
   - menjalankan seluruh quality gate (`composer audit`, Pint, test),
   - `composer install --no-dev`,
   - build asset Vite,
   - membuat `release.tar.gz`,
   - upload ke server lewat SCP,
   - ekstrak ke `laravel/releases/<commit>`,
   - symlink `laravel/current`,
   - jalankan migrasi dan cache Laravel,
   - menyalin file publik ke `public_html`,
   - membuat `index.php` yang mengarah ke `laravel/current`.

Jika workflow gagal karena `.env` belum ada, buat dulu file pada langkah 4.

## 6. Setelah deploy pertama

Jalankan seeder produksi satu kali:

```bash
cd ~/domains/tapaksucisportacademy.com/laravel/current
php artisan db:seed --class=ProductionSeeder --force
```

`ProductionSeeder` hanya membuat:

- akun admin dari `ADMIN_EMAIL` / `ADMIN_PASSWORD`;
- jenis pembayaran dasar (SPP, pendaftaran, UKT, event).

Seeder ini **tidak** membuat siswa, pembayaran, absensi, atau konten demo.

Cek aplikasi:

```bash
curl -I https://tapaksucisportacademy.com/up
```

Lalu buka:

- `https://tapaksucisportacademy.com`
- `https://tapaksucisportacademy.com/admin`

## 7. Cron scheduler dan queue

Tambahkan dua cron job di hPanel. Ganti path dan `php` sesuai server.

### Scheduler Laravel

```cron
* * * * * cd /home/u123456789/domains/tapaksucisportacademy.com/laravel/current && /usr/bin/php8.2 artisan schedule:run >> /dev/null 2>&1
```

### Queue worker (pengganti Supervisor)

```cron
* * * * * cd /home/u123456789/domains/tapaksucisportacademy.com/laravel/current && /usr/bin/php8.2 artisan queue:work --stop-when-empty --max-time=50 --tries=3 >> /dev/null 2>&1
```

`--stop-when-empty` dan `--max-time=50` mencegah worker menggantung terlalu lama
di shared hosting. Jika tersedia `flock`, gunakan lock agar tidak ada dua worker
sekaligus:

```cron
* * * * * flock -n /tmp/tapak-suci-queue.lock bash -lc 'cd /home/u123456789/domains/tapaksucisportacademy.com/laravel/current && /usr/bin/php8.2 artisan queue:work --stop-when-empty --max-time=50 --tries=3' >> /dev/null 2>&1
```

## 8. Rollback

Workflow menyimpan 3 release terakhir. Untuk kembali ke release sebelumnya:

```bash
cd ~/domains/tapaksucisportacademy.com/laravel
ls -1dt releases/*/
ln -sfn "$(ls -1dt releases/*/ | sed -n '2p')" current
cd current
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Salin ulang file publik:

```bash
rsync -a --delete \
  --exclude='.well-known' --exclude='index.php' --exclude='storage' \
  ~/domains/tapaksucisportacademy.com/laravel/current/public/ \
  ~/domains/tapaksucisportacademy.com/public_html/
```

Rollback kode **tidak** otomatis membatalkan migrasi database. Jika migrasi
terakhir merusak data, restore dari backup Hostinger.

## 9. Troubleshooting

| Gejala | Pemeriksaan |
|---|---|
| 500 Server Error | `storage/logs/laravel-*.log`, permission `shared/storage`, isi `.env`, pastikan `open_basedir` mencakup folder `laravel/` |
| 404 asset/CSS | pastikan `public_html` berisi hasil copy `public/` dan `.htaccess` ada |
| Admin tidak bisa login | cek `APP_URL`, session driver, dan migrasi tabel sessions |
| SSH permission denied | cek host/port/user, public key di hPanel, private key tanpa passphrase |
| Deploy gagal di `route:cache` | cek route closure; jalankan `php artisan route:cache` manual |
| Queue tidak jalan | cek cron, `php artisan queue:work --once`, `failed_jobs` |
| Upload lambat | artifact berisi `vendor`; pastikan koneksi SSH stabil |

Perintah diagnosa:

```bash
cd ~/domains/tapaksucisportacademy.com/laravel/current
php artisan about
php artisan optimize:clear
php artisan queue:failed
tail -n 100 ~/domains/tapaksucisportacademy.com/laravel/shared/storage/logs/laravel-$(date +%F).log
```

## 10. Keamanan dan backup

- `APP_DEBUG=false`, `APP_ENV=production`.
- `.env` hanya di `shared/`, permission `600`.
- Jangan commit `.env` atau kredensial FTP/SSH.
- Gunakan environment `production` di GitHub dan batasi akses secrets.
- Aktifkan backup harian Hostinger dan uji restore sebelum go-live.
- Backup manual sebelum migrasi besar:
  ```bash
  mysqldump --single-transaction --routines --triggers \
    -u USER -p DATABASE | gzip > ~/backup-$(date +%F-%H%M).sql.gz
  ```
- Simpan backup di luar server aplikasi.
