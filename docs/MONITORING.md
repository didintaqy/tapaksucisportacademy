# Monitoring & Logging

Dokumen ini menjelaskan cara memantau aplikasi produksi tanpa vendor error tracking
berbayar. Keputusan: **logs-only + uptime alert** (bukan Sentry/Flare).

## 1. Logging

### Konfigurasi produksi

```dotenv
LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=warning
LOG_DAILY_DAYS=30
```

- `daily` menulis satu file per hari: `storage/logs/laravel-YYYY-MM-DD.log`.
- `warning` mencatat warning, error, critical, alert, dan emergency tanpa noise `debug`.
- Retensi 30 hari; file lama dihapus otomatis oleh Monolog.
- Pastikan `storage/logs` dapat ditulis user web (`www-data`) dan tidak dapat diakses publik.

### Perintah pemeriksaan

```bash
# Ringkasan error hari ini
grep -E "\.(ERROR|CRITICAL|ALERT|EMERGENCY)" storage/logs/laravel-$(date +%F).log | tail -50

# Ikuti log secara real-time
tail -f storage/logs/laravel-$(date +%F).log

# Cek permission & ukuran log
ls -lh storage/logs/
```

Jangan pernah mengirim file log mentah ke publik karena dapat memuat data pribadi
(email, nomor telepon, payload gateway). Redaksi data sensitif sebelum dibagikan.

### Rotasi & disk

`daily` sudah menangani rotasi. Tetap pantau kapasitas disk:

```bash
df -h
du -sh storage/logs
```

Jika disk > 80%, hapus log lama atau turunkan `LOG_DAILY_DAYS` sementara.

## 2. Uptime monitoring

Endpoint health bawaan Laravel: **`GET /up`** (HTTP 200 jika aplikasi hidup).

Gunakan salah satu layanan gratis:

- **UptimeRobot** — monitor HTTP(s) setiap 5 menit, alert email/Telegram.
- **Better Stack Uptime** — interval 1 menit, status page opsional.
- **Healthchecks.io** — untuk cron/scheduler, bukan HTTP uptime.

Konfigurasi yang disarankan:

| Pengaturan | Nilai |
|---|---|
| URL | `https://domain-anda.com/up` |
| Interval | 1–5 menit |
| Timeout | 10 detik |
| Alert | Email admin + Telegram/WhatsApp opsional |
| Retry | 2–3 kali sebelum alert |

Uji manual:

```bash
curl -i https://domain-anda.com/up
```

Respons yang diharapkan: `HTTP/2 200` dengan body `OK`.

## 3. Queue monitoring

Newsletter, email kontak, pendaftaran, dan pengingat tagihan diproses queue worker.

```bash
# Jumlah job menunggu di queue default
php artisan queue:monitor default --max=100

# Daftar job gagal
php artisan queue:failed

# Coba ulang satu job
php artisan queue:retry <id>

# Coba ulang semua job gagal
php artisan queue:retry all
```

- Worker Supervisor harus berjalan; lihat `docs/DEPLOYMENT.md`.
- Setiap deploy, jalankan `php artisan queue:restart` agar worker memuat kode baru.
- `queue:prune-failed --hours=168` berjalan otomatis setiap pukul 03:00.
- Jika `failed_jobs` bertambah terus, periksa kredensial SMTP dan log worker.

## 4. Scheduler monitoring

```bash
php artisan schedule:list
```

Cron wajib aktif:

```cron
* * * * * cd /path/ke/project && php artisan schedule:run >> /dev/null 2>&1
```

Jadwal penting:

| Waktu | Perintah | Fungsi |
|---|---|---|
| 08:00 | `payments:send-reminders --days=3` | Pengingat tagihan |
| 03:00 | `queue:prune-failed --hours=168` | Bersihkan failed job lama |

Gunakan Healthchecks.io (atau sejenis) untuk memastikan `schedule:run` benar-benar
dipanggil setiap menit. Buat check dengan periode 1 menit dan grace 5 menit.

## 5. Pemeriksaan rutin

| Frekuensi | Tindakan |
|---|---|
| Setiap hari | Cek alert uptime, error log, failed job |
| Setiap minggu | Cek disk, ukuran database, backup terakhir |
| Setiap bulan | Uji restore backup, review akses admin, update dependency |
| Setiap deploy | `queue:restart`, cek `/up`, cek log 15 menit pertama |

## 6. Alur respons insiden

1. **Pastikan dampak** — cek `/up`, halaman publik utama, dan login admin.
2. **Cek log** — `storage/logs/laravel-YYYY-MM-DD.log`, cari `ERROR`/`CRITICAL`.
3. **Cek queue** — `php artisan queue:failed`; retry jika penyebab sementara.
4. **Cek scheduler** — `php artisan schedule:list`; pastikan cron aktif.
5. **Rollback** jika deploy terakhir menjadi penyebab: checkout tag/commit stabil,
   `composer install --no-dev`, `php artisan migrate --force` (hati-hati migrasi),
   lalu `php artisan queue:restart`.
6. **Catat** kronologi, penyebab, dan tindakan pencegahan.

## 7. Keputusan

- **Error tracking:** logs-only + uptime alert (21-10-2026).
- **Vendor Sentry/Flare:** tidak dipakai saat ini; dapat ditambahkan nanti tanpa
  mengubah alur logging karena Laravel memakai channel `stack`.
