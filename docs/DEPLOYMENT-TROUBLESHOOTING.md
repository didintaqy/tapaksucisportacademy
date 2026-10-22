# Deployment Hostinger: Catatan Kasus dan Runbook Perbaikan

Dokumen ini merangkum seluruh kasus deployment yang kita temui, penyebabnya, perbaikannya, dan langkah pencegahan agar kesalahan yang sama tidak terulang.

Status terakhir: **seluruh tabel database MySQL sudah dihapus**. Database akan dibuat ulang dari migrasi. Ikuti bagian [Pemulihan database kosong](#pemulihan-database-kosong) sebelum menjalankan deploy berikutnya.

- Repo: `zendzo/tapaksucisportacademy`
- Branch deploy: `main`
- Domain: `https://tapaksucisportacademy.com`
- Hosting: Hostinger Premium, SSH port biasanya `65002`
- Layout server:
  - Aplikasi: `~/domains/tapaksucisportacademy.com/laravel`
  - Public: `~/domains/tapaksucisportacademy.com/public_html`
  - Env: `~/domains/tapaksucisportacademy.com/laravel/shared/.env`
- Commit kode terakhir: `9558b20` (dokumen ini dibuat setelah commit tersebut)

---

## Pemulihan database kosong

Karena semua tabel sudah dihapus, jangan langsung menjalankan deploy sebelum `.env` dan urutan migrasi benar.

### 1. Masuk ke server

```bash
ssh -p 65002 uXXXXXXXXX@IP_SERVER
```

Ganti `uXXXXXXXXX` dan `IP_SERVER` dengan username serta IP/host SSH dari hPanel.

### 2. Cek `.env` produksi

```bash
cd ~/domains/tapaksucisportacademy.com/laravel/current

grep -E '^(APP_ENV|APP_DEBUG|APP_URL|APP_KEY|DB_CONNECTION|DB_HOST|DB_PORT|DB_DATABASE|DB_USERNAME|CACHE_STORE|SESSION_DRIVER|QUEUE_CONNECTION|ADMIN_EMAIL)=' .env
```

Nilai yang wajib benar:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tapaksucisportacademy.com
APP_KEY=base64:...

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=uXXXXXXXXX_tssa_database
DB_USERNAME=uXXXXXXXXX_tssa_database
DB_PASSWORD=PASSWORD_DATABASE

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

ADMIN_NAME="Administrator"
ADMIN_EMAIL=admin@tapaksucisportacademy.com
ADMIN_PASSWORD=PASSWORD_ADMIN_KUAT
```

Catatan:

- `DB_DATABASE` adalah **nama database MySQL**, bukan path file SQLite.
- `DB_CONNECTION` yang menentukan driver, bukan `DB_DATABASE`.
- Jika `APP_KEY` kosong, buat di lokal dengan `php artisan key:generate --show`, lalu tempel ke `.env` produksi. Jangan mengganti `APP_KEY` jika sudah ada data terenkripsi.
- `ProductionSeeder` membutuhkan `ADMIN_EMAIL` dan `ADMIN_PASSWORD`.

### 3. Bersihkan config cache

```bash
php artisan config:clear
```

Jika `php` tidak dikenali, gunakan binary yang sama dengan secret `PHP_BIN`, misalnya:

```bash
/usr/bin/php8.2 artisan config:clear
```

### 4. Jalankan migrasi dari awal

Database sudah kosong, jadi cukup:

```bash
php artisan migrate --force
```

Jangan gunakan `migrate:fresh` di produksi kecuali Anda yakin tidak ada data penting. `migrate:fresh` menghapus semua tabel.

### 5. Jalankan seeder produksi

```bash
php artisan db:seed --class=ProductionSeeder --force
```

Seeder ini hanya membuat:

- akun admin dari `ADMIN_EMAIL` / `ADMIN_PASSWORD`
- jenis pembayaran awal

Tidak membuat siswa demo, konten placeholder, atau akun demo.

Jika seeder dijalankan belakangan setelah `config:cache`, jalankan `php artisan config:clear` terlebih dahulu. `ProductionSeeder` membaca `ADMIN_EMAIL` dan `ADMIN_PASSWORD` lewat `env()`, dan saat config sedang di-cache file `.env` tidak dimuat ulang.

### 6. Bersihkan dan cache ulang

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Urutan ini penting:

```text
config:clear -> migrate -> seed -> optimize:clear -> config:cache
```

`optimize:clear` harus setelah migrasi karena `CACHE_STORE=database` membutuhkan tabel `cache`.

### 7. Verifikasi

```bash
php artisan migrate:status
php artisan about
curl -I https://tapaksucisportacademy.com/up
curl -I https://tapaksucisportacademy.com/admin
```

Kalau `/up` masih 404, cek `public_html`:

```bash
ls -la ~/domains/tapaksucisportacademy.com/public_html
```

Pastikan:

- `index.php` ada
- `index.html` placeholder Hostinger tidak ada
- `.htaccess` ada
- `storage` symlink ada

### 8. Jalankan deploy dari GitHub

Setelah database dan `.env` siap, jalankan workflow baru:

**GitHub → Actions → Deploy Production → Run workflow → branch `main`**

Jangan me-re-run run lama. GitHub memakai workflow dari commit run tersebut, bukan workflow terbaru.

---

## Ringkasan kasus

| Kasus | Gejala utama | Penyebab | Perbaikan |
|---|---|---|---|
| 1 | `tar: .: file changed as we read it` | Arsip ditulis di dalam direktori yang sedang diarsipkan | Arsip dibuat di `$RUNNER_TEMP` |
| 2 | `Secret SSH_PRIVATE_KEY belum diisi` | Secret tidak dipetakan ke env step validasi | Tambah `env: SSH_PRIVATE_KEY` di step validasi |
| 3 | `Permission denied (publickey,password)` | Public key tidak terpasang di Hostinger / key salah | Pasang `.pub` di hPanel, cek fingerprint, host, user, port |
| 4 | `Database file at path [...] does not exist` | `.env` masih `DB_CONNECTION=sqlite` | Ubah ke `mysql`, `config:clear` |
| 5 | `optimize:clear` gagal di database baru | `cache:clear` dijalankan sebelum tabel `cache` dibuat | `config:clear` → `migrate` → `optimize:clear` |
| 6 | `Unknown column 'email' in 'students'` | Migrasi `add_branch` memakai `after('email')` sebelum kolom email dibuat | Hapus `after('email')` |
| 7 | `Table 'post_tag'` / FK gagal | Tabel `tags` dibuat setelah `post_tag` | Rename `create_tags` agar berjalan lebih dulu |
| 8 | FK `program_id` gagal | Tabel `programs` dibuat setelah migrasi FK | Rename `add_program_id` agar berjalan setelah `create_programs` |
| 9 | `Table 'tags' already exists` | Rename migration setelah tabel terlanjur dibuat | Rekonsiliasi tabel `migrations` |

---

## Kasus 1: `tar: .: file changed as we read it`

### Gejala

```text
tar: .: file changed as we read it
##[error]Process completed with exit code 1.
```

Workflow gagal di step **Create release archive**.

### Penyebab

Workflow menjalankan:

```bash
tar ... -czf release.tar.gz .
```

Arsip `release.tar.gz` dibuat **di dalam** direktori `.` yang sedang diarsipkan. Saat `tar` membaca file tersebut, ukurannya berubah karena sedang ditulis. `tar` mendeteksi perubahan itu dan keluar dengan kode 1.

### Perbaikan

Arsip dibuat di luar workspace:

```bash
ARCHIVE_PATH="$RUNNER_TEMP/tapak-suci-release.tar.gz"
rm -f "$ARCHIVE_PATH"

tar \
  ... \
  -czf "$ARCHIVE_PATH" .

echo "ARCHIVE_PATH=$ARCHIVE_PATH" >> "$GITHUB_ENV"
```

Step upload memakai:

```bash
scp ... "$ARCHIVE_PATH" "$SSH_USER@$SSH_HOST:$DEPLOY_PATH/release.tar.gz"
```

Tambahkan juga exclude untuk arsip lama:

```bash
--exclude='./release.tar.gz'
--exclude='./tapak-suci-release.tar.gz'
```

### Pelajaran

Jangan pernah menulis file arsip di dalam direktori yang sedang diarsipkan. Buat di `$RUNNER_TEMP`, `/tmp`, atau direktori lain di luar source tree.

---

## Kasus 2: validasi secret salah membaca `SSH_PRIVATE_KEY`

### Gejala

```text
Error: Secret SSH_PRIVATE_KEY belum diisi.
Error: Process completed with exit code 1.
```

Padahal secret sudah diisi di GitHub.

### Penyebab

Step validasi memeriksa variabel shell:

```bash
for name in SSH_HOST SSH_USER SSH_PRIVATE_KEY DEPLOY_PATH PUBLIC_PATH; do
  if [ -z "${!name:-}" ]; then
    ...
  fi
done
```

`SSH_PRIVATE_KEY` tidak ada di `env` step tersebut. Akibatnya nilainya selalu kosong, walaupun secret di GitHub ada.

### Perbaikan

Tambahkan secret ke env step validasi:

```yaml
- name: Validate deployment secrets
  env:
    SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
  run: |
    missing=0
    ...
```

Step `Setup SSH` tetap memakai secret langsung untuk menulis file key.

### Pelajaran

Setiap secret yang diperiksa lewat shell harus dipetakan ke `env` step tersebut. Secret yang hanya dipakai di `${{ secrets.X }}` tidak otomatis menjadi environment variable.

---

## Kasus 3: `Permission denied (publickey,password)`

### Gejala

```text
***@***: Permission denied (publickey,password).
Error: Process completed with exit code 255.
```

### Penyebab

SSH berhasil mencapai server, tetapi server menolak key. Kemungkinan:

- Public key belum ditambahkan di hPanel
- Key ditambahkan ke akun/website yang salah
- Yang dimasukkan ke secret adalah public key, bukan private key
- Private key memiliki passphrase
- Private key terpotong atau mengandung CRLF
- `SSH_USER`, `SSH_HOST`, atau `SSH_PORT` salah
- Key ditambahkan di menu VPS, padahal hosting ini shared hosting

### Diagnosis

Cek fingerprint lokal:

```bash
ssh-keygen -lf ~/.ssh/tapak_suci_deploy.pub
```

Fingerprint di log GitHub harus sama:

```text
Deploy key fingerprint: SHA256:...
```

Fingerprint yang sama hanya membuktikan private key di GitHub cocok dengan public key lokal. Itu **belum** membuktikan key sudah diotorisasi server.

Test manual dengan public key saja:

```bash
ssh -vvv -i ~/.ssh/tapak_suci_deploy \
  -p 65002 \
  -o IdentitiesOnly=yes \
  -o PreferredAuthentications=publickey \
  uXXXXXXXXX@IP_SERVER
```

Cari baris:

```text
Offering public key: ...
Server accepts key: ...
```

Jika masih:

```text
Authentications that can continue: publickey,password
```

berarti key ditolak.

### Perbaikan di Hostinger

Untuk shared hosting, key harus ditambahkan di:

**hPanel → Websites → pilih domain → Dashboard → Advanced → SSH Access → SSH Keys → Add SSH key**

Bukan di:

```text
VPS → Settings → SSH keys
```

Yang ditempel adalah isi file `.pub`:

```bash
cat ~/.ssh/tapak_suci_deploy.pub
```

Pastikan key diawali:

```text
ssh-ed25519 ...
```

atau:

```text
ssh-rsa ...
```

Bukan:

```text
-----BEGIN OPENSSH PRIVATE KEY-----
```

### Jika key dibuat dengan passphrase

GitHub Actions tidak bisa memasukkan passphrase. Buat key tanpa passphrase:

```bash
ssh-keygen -t ed25519 -C "github-actions-tapak-suci" \
  -f ~/.ssh/tapak_suci_deploy -N ""
```

Lalu:

1. Ganti secret `SSH_PRIVATE_KEY` dengan isi private key baru.
2. Tambahkan public key baru di hPanel.
3. Test manual.
4. Jalankan workflow baru.

### Hardening workflow

Workflow sekarang:

- Menghapus CRLF:

  ```bash
  printf '%s\n' "${{ secrets.SSH_PRIVATE_KEY }}" | tr -d '\r' > ~/.ssh/deploy_key
  ```

- Memvalidasi key dan menolak passphrase:

  ```bash
  ssh-keygen -y -P '' -f ~/.ssh/deploy_key
  ```

- Menampilkan fingerprint:

  ```bash
  echo "Deploy key fingerprint: $(ssh-keygen -lf ~/.ssh/deploy_key.pub | awk '{print $2}')"
  ```

- Memaksa public key authentication:

  ```bash
  -o IdentitiesOnly=yes
  -o PreferredAuthentications=publickey
  ```

### Pelajaran

Fingerprint valid bukan berarti key sudah terpasang di server. Harus ada dua hal:

1. Private key di GitHub cocok dengan public key lokal.
2. Public key tersebut ada di `authorized_keys` akun SSH Hostinger.

---

## Kasus 4: produksi memakai SQLite

### Gejala

```text
Database file at path [***_tssa_database] does not exist.
(Connection: sqlite, Database: uXXXXXXXXX_tssa_database,
SQL: delete from "cache")
```

### Penyebab

`.env` produksi masih berisi:

```dotenv
DB_CONNECTION=sqlite
```

`.env.example` Laravel memang default ke SQLite. `DB_DATABASE` hanya nama database; itu tidak mengganti driver. Karena driver-nya SQLite, nama database MySQL dianggap sebagai path file SQLite.

Error muncul di `delete from "cache"` karena:

```dotenv
CACHE_STORE=database
```

Laravel mencoba membersihkan cache database, tetapi file SQLite tidak ada.

### Perbaikan

Ubah `.env` produksi:

```dotenv
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=uXXXXXXXXX_tssa_database
DB_USERNAME=uXXXXXXXXX_tssa_database
DB_PASSWORD=PASSWORD_DATABASE
```

Lalu:

```bash
cd ~/domains/tapaksucisportacademy.com/laravel/current
php artisan config:clear
```

Workflow sekarang juga memeriksa:

```bash
grep -Eq '^DB_CONNECTION=mysql[[:space:]]*$' "$APP_PATH/shared/.env"
```

Jika bukan `mysql`, deploy berhenti dengan pesan jelas.

### Pelajaran

- `DB_CONNECTION` menentukan driver.
- `DB_DATABASE` adalah nama database, bukan path.
- Setelah mengubah `.env`, selalu `php artisan config:clear`.
- Config cache bisa menyimpan nilai SQLite lama walaupun `.env` sudah diubah.

---

## Kasus 5: urutan `optimize:clear` dan migrasi

### Gejala

Deploy gagal saat database baru/kosong, biasanya di `cache:clear` atau `optimize:clear`:

```text
Table 'cache' doesn't exist
```

atau error cache lain.

### Penyebab

Urutan lama:

```bash
php artisan optimize:clear
php artisan migrate --force
```

`optimize:clear` menjalankan `cache:clear`. Dengan `CACHE_STORE=database`, Laravel butuh tabel `cache`. Pada database baru, tabel itu belum dibuat karena migrasi belum jalan.

### Perbaikan

Urutan sekarang:

```bash
php artisan package:discover --ansi
php artisan config:clear
php artisan migrate --force --no-interaction
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

`config:clear` tidak membutuhkan database. Setelah migrasi membuat tabel `cache`, baru `optimize:clear` aman.

---

## Kasus 6: `Unknown column 'email' in 'students'`

### Gejala

```text
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'email' in 'students'
SQL: alter table `students` add `branch` varchar(255) null after `email`
```

### Penyebab

Migrasi:

```text
2026_10_20_172123_add_branch_to_students_table
```

berjalan sebelum:

```text
2026_10_20_225806_add_email_to_students_table
```

Tetapi migrasi branch memakai:

```php
$table->string('branch')->nullable()->after('email');
```

Kolom `email` belum ada saat migrasi itu jalan.

SQLite tidak mempermasalahkan `AFTER`, sehingga test lokal lolos. MySQL gagal.

### Perbaikan

Hapus `after('email')`:

```php
$table->string('branch')->nullable();
```

Posisi kolom tidak penting secara fungsional.

### Pelajaran

- Jangan memakai `after('kolom')` untuk kolom yang dibuat migrasi lain yang berjalan lebih lambat.
- Test migrasi di MySQL, bukan hanya SQLite.

---

## Kasus 7: `post_tag` dibuat sebelum `tags`

### Gejala

Pada MySQL, migrasi `post_tag` bisa gagal karena foreign key mereferensikan tabel `tags` yang belum ada.

### Penyebab

Nama file lama:

```text
2026_10_21_005747_create_post_tag_table.php
2026_10_21_005747_create_tags_table.php
```

Urutan alfabet:

```text
create_post_tag  <  create_tags
```

Jadi `post_tag` berjalan lebih dulu, padahal membuat FK ke `tags`.

### Perbaikan

Rename migrasi `tags` agar berjalan lebih dulu:

```text
2026_10_21_005747_create_tags_table.php
→ 2026_10_21_005746_create_tags_table.php
```

### Pelajaran

Tabel yang direferensikan harus dibuat sebelum migrasi yang membuat foreign key.

---

## Kasus 8: `program_id` dibuat sebelum tabel `programs`

### Gejala

Pada MySQL, migrasi `add_program_id` bisa gagal karena FK mereferensikan tabel `programs` yang belum ada.

### Penyebab

Nama file lama:

```text
2026_10_21_022046_add_program_id_to_students_training_schedules_and_registrations.php
2026_10_21_022046_create_programs_table.php
```

Urutan alfabet:

```text
add_program_id  <  create_programs
```

Jadi `add_program_id` berjalan lebih dulu.

### Perbaikan

Rename migrasi `add_program_id` agar berjalan setelah `create_programs`:

```text
2026_10_21_022046_add_program_id_...
→ 2026_10_21_022047_add_program_id_...
```

### Pelajaran

Sama seperti kasus 7: buat tabel induk sebelum foreign key.

---

## Kasus 9: `Table 'tags' already exists` setelah rename migration

### Gejala

```text
SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'tags' already exists
```

### Penyebab

Laravel mencatat migration berdasarkan **nama file**. Setelah file di-rename:

```text
2026_10_21_005747_create_tags_table
→ 2026_10_21_005746_create_tags_table
```

Laravel menganggap nama baru belum pernah jalan, walaupun tabel `tags` sudah ada.

### Perbaikan

Cek isi tabel `migrations`:

```bash
cd ~/domains/tapaksucisportacademy.com/laravel/current

php artisan tinker --execute='dump(DB::table("migrations")->where("migration", "like", "%tags%")->orWhere("migration", "like", "%program%")->pluck("migration")->all());'
```

Cek kolom tabel:

```bash
php artisan tinker --execute='dump(Schema::getColumnListing("tags"));'
```

#### Jika nama lama masih tercatat

```bash
php artisan tinker --execute='DB::table("migrations")->where("migration", "2026_10_21_005747_create_tags_table")->update(["migration" => "2026_10_21_005746_create_tags_table"]);'
```

#### Jika tidak ada row, tetapi tabel `tags` sudah ada dan kolomnya sesuai

```bash
php artisan tinker --execute='DB::table("migrations")->insert(["migration" => "2026_10_21_005746_create_tags_table", "batch" => DB::table("migrations")->max("batch") ?: 1]);'
```

#### Jika migrasi `add_program_id` juga di-rename

```bash
php artisan tinker --execute='DB::table("migrations")->where("migration", "2026_10_21_022046_add_program_id_to_students_training_schedules_and_registrations")->update(["migration" => "2026_10_21_022047_add_program_id_to_students_training_schedules_and_registrations"]);'
```

Setelah rekonsiliasi:

```bash
php artisan migrate --force
```

### Pelajaran

- Jangan rename migration yang sudah pernah jalan di production.
- Jika terpaksa rename, rekonsiliasi tabel `migrations`.
- Jangan langsung `DROP TABLE` sebelum tahu apakah tabel itu dibuat aplikasi atau tabel lama.
- `migrate:fresh` hanya untuk database yang boleh dihapus.

---

## Perubahan kode dan commit

| Commit | Perubahan |
|---|---|
| `c408e9a` | Arsip release dibuat di `$RUNNER_TEMP`, bukan di root repo |
| `6564937` | Merge perbaikan arsip ke `main` |
| `dca1790` | `SSH_PRIVATE_KEY` dipetakan ke env step validasi |
| `5dc9afa` | Merge perbaikan validasi secret |
| `67669e7` | Hardening SSH: CRLF, validasi key, fingerprint, publickey-only |
| `fbd8324` | Merge hardening SSH |
| `c6538f9` | Validasi `DB_CONNECTION=mysql`, urutan `config:clear` → migrate → optimize:clear |
| `7d47126` | Merge perbaikan database |
| `dfdebaa` | Perbaikan urutan migrasi MySQL |
| `9558b20` | Merge perbaikan migrasi ke `main` |

---

## Checklist deploy pertama

- [ ] `.env` produksi ada di `laravel/shared/.env`
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://tapaksucisportacademy.com`
- [ ] `APP_KEY` terisi
- [ ] `DB_CONNECTION=mysql`
- [ ] `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` benar
- [ ] `ADMIN_EMAIL` dan `ADMIN_PASSWORD` terisi
- [ ] Database MySQL dan user sudah dibuat di hPanel
- [ ] Public SSH key terpasang di **Website → Advanced → SSH Access**
- [ ] Secret GitHub `SSH_PRIVATE_KEY` berisi private key tanpa passphrase
- [ ] `SSH_HOST`, `SSH_USER`, `SSH_PORT`, `DEPLOY_PATH`, `PUBLIC_PATH` benar
- [ ] `php artisan config:clear` sudah dijalankan
- [ ] `php artisan migrate --force` berhasil
- [ ] `php artisan db:seed --class=ProductionSeeder --force` berhasil
- [ ] `php artisan optimize:clear` berhasil
- [ ] `php artisan config:cache`, `route:cache`, `view:cache` berhasil
- [ ] Workflow **Deploy Production** memakai run baru, bukan re-run run lama
- [ ] `https://tapaksucisportacademy.com/up` merespons 200
- [ ] `/admin` bisa dibuka
- [ ] Frontend tampil, bukan halaman “Under Development”
- [ ] `public_html/index.html` placeholder tidak ada
- [ ] Cron scheduler dan queue sudah dipasang

### Cron Hostinger

Tambahkan lewat **hPanel → Advanced → Cron Jobs**:

```cron
* * * * * cd ~/domains/tapaksucisportacademy.com/laravel/current && php artisan schedule:run >> /dev/null 2>&1
* * * * * cd ~/domains/tapaksucisportacademy.com/laravel/current && php artisan queue:work --stop-when-empty --max-time=50 --tries=3 >> /dev/null 2>&1
```

Jika `php` tidak dikenali di cron, gunakan path binary yang sama dengan `PHP_BIN`.

---

## Checklist sebelum mengubah migration

- [ ] Apakah migration ini sudah pernah jalan di production?
- [ ] Jika sudah, jangan rename atau ubah urutannya.
- [ ] Jika harus rename, siapkan rekonsiliasi tabel `migrations`.
- [ ] Apakah `after('kolom')` merujuk kolom yang sudah ada pada saat migrasi berjalan?
- [ ] Apakah tabel yang direferensikan foreign key sudah dibuat lebih dulu?
- [ ] Apakah sudah dites di MySQL, bukan hanya SQLite?
- [ ] Apakah ada backup database sebelum migrasi?
- [ ] Apakah `php artisan migrate:status` bersih sebelum deploy?

---

## Perintah diagnosa

### Aplikasi

```bash
cd ~/domains/tapaksucisportacademy.com/laravel/current

php artisan about
php artisan migrate:status
php artisan db:show
php artisan queue:failed
```

### Log

```bash
tail -n 100 ~/domains/tapaksucisportacademy.com/laravel/shared/storage/logs/laravel-$(date +%F).log
```

### HTTP

```bash
curl -I https://tapaksucisportacademy.com/up
curl -I https://tapaksucisportacademy.com/admin
curl -I https://tapaksucisportacademy.com/
```

### SSH

```bash
ssh -vvv -i ~/.ssh/tapak_suci_deploy \
  -p 65002 \
  -o IdentitiesOnly=yes \
  -o PreferredAuthentications=publickey \
  uXXXXXXXXX@IP_SERVER
```

### GitHub Actions

```bash
gh run list --limit 10
gh run view <RUN_ID> --log-failed
```

Jika tidak ada `gh`, buka:

```text
https://github.com/zendzo/tapaksucisportacademy/actions
```

Pilih run terbaru, buka step yang merah, lalu copy 20–50 baris terakhir.

---

## Pencegahan

1. **Jangan re-run run lama setelah workflow diubah.** GitHub memakai workflow dari commit run tersebut. Gunakan run baru atau `workflow_dispatch`.
2. **Selalu cek fingerprint SSH.** Fingerprint cocok berarti key pair benar; belum tentu key sudah terpasang di server.
3. **Public key di hPanel, private key di GitHub.** Jangan tertukar.
4. **Gunakan key tanpa passphrase untuk CI.**
5. **`DB_CONNECTION` harus `mysql` di production.** `DB_DATABASE` bukan penentu driver.
6. **`config:clear` setelah mengubah `.env`.**
7. **Urutan deploy: `config:clear` → `migrate` → `optimize:clear`.**
8. **Test migrasi di MySQL.** SQLite tidak menegakkan urutan `AFTER` dan foreign key seperti MySQL.
9. **Jangan rename migration yang sudah jalan.** Jika terpaksa, rekonsiliasi tabel `migrations`.
10. **Buat tabel induk sebelum foreign key.**
11. **Backup database sebelum migrasi production.**
12. **Jangan commit `.env`, private key, atau password.**
13. **`ProductionSeeder` hanya untuk instalasi pertama.** Jangan seed data demo di production.
14. **Setelah deploy, cek `/up`, `/admin`, dan frontend.**
15. **Pastikan `public_html/index.html` placeholder tidak menutupi `index.php`.**

---

## Referensi

- `docs/DEPLOYMENT-HOSTINGER.md` — setup Hostinger, secrets, cron, rollback
- `docs/MONITORING.md` — monitoring logs, uptime, queue
- `docs/TODO.md` — backlog dan keputusan proyek
- `.github/workflows/deploy.yml` — pipeline deploy production
- `.github/workflows/ci.yml` — quality gate
