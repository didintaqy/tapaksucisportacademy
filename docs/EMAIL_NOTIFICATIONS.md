# Email Notification

Semua email transaksional dikirim melalui **queue** (`implements ShouldQueue`) agar request pengguna tidak menunggu proses SMTP.

## Daftar Mailable

| Mailable | Pemicu | Penerima | Template |
|---|---|---|---|
| `ContactFormReceivedMail` | Form kontak publik dikirim | Admin (`MAIL_FROM_ADDRESS`) | `emails/contact-received.blade.php` |
| `RegistrationReceivedMail` | Pendaftaran siswa baru dikirim | Admin (`MAIL_FROM_ADDRESS`) | `emails/registration-received.blade.php` |
| `PaymentReminderMail` | Command `payments:send-reminders` atau aksi **Pengingat** di tabel pembayaran | `students.email` (wali/siswa) | `emails/payment-reminder.blade.php` |

Template memakai layout `resources/views/layout/emails/master.blade.php`.

## Konfigurasi `.env`

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.provider.com
MAIL_PORT=587
MAIL_USERNAME=username
MAIL_PASSWORD=password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=admin@tapaksuci.academy
MAIL_FROM_NAME="Tapak Suci Sport Academy"

QUEUE_CONNECTION=database
```

Untuk development, gunakan Mailpit/MailHog:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_ENCRYPTION=null
```

atau `MAIL_MAILER=log` untuk menulis email ke `storage/logs/laravel.log`.

## Menjalankan Queue

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

Di produksi jalankan lewat Supervisor (lihat `docs/DEPLOYMENT.md`). Tanpa worker, email akan tetap berada di tabel `jobs` dan tidak terkirim.

## Pengingat Tagihan

Pengingat dijalankan otomatis oleh scheduler setiap hari pukul 08:00:

```bash
php artisan payments:send-reminders --days=3
```

Opsi:

- `--days=N` — kirim untuk tagihan yang jatuh tempo dalam N hari ke depan (termasuk yang sudah lewat).
- `--dry-run` — tampilkan daftar tanpa mengirim email atau mengubah data.
- `--limit=N` — batasi jumlah tagihan yang diproses.

Setelah email masuk queue, `payments.reminded_at` diisi dan `reminder_count` bertambah. Tagihan yang sudah diingatkan pada hari yang sama tidak akan dikirim ulang.

Siswa tanpa `email` akan dilewati. Pastikan email wali diisi di **Master Data → Daftar Siswa**.

## Testing

- Manual: kirim pesan kontak/pendaftaran lalu cek Mailpit, atau jalankan `php artisan payments:send-reminders --dry-run`.
- Automated: gunakan `Mail::fake()` dan `Mail::assertQueued(...)`; lihat `tests/Feature/PaymentReminderTest.php`.

## Catatan Penting

- Jangan mengirim email langsung dengan `Mail::send()` di controller; gunakan `Mail::to(...)->queue(...)`.
- Semua Mailable menerima model dan menggunakan `SerializesModels`, jadi aman disimpan di queue.
- Jika mengubah template, uji render dengan `$mailable->render()` pada feature test.
