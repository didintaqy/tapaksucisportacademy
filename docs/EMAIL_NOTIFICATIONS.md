# Email Notification

Notifikasi email send secara *sync* (perhatian tidak melalui queue), karena notifikasi hanya ke admin dan contoh kecil. Untuk notifikasi berat, cukup ganti Mailable ke `implements ShouldQueue`.

## Konfigurasi Tambahan

Tambahkan pengaturan email ke `.env` produksi:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=mailhog # atau sesuai SMTP provider
MAIL_PORT=1025
MAIL_USERNAME=username
MAIL_PASSWORD=password
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=admin@tapaksuci.academy
MAIL_FROM_NAME="Tapak Suci Sport Academy"
```

Dalam mode development, setting gunakan `mailhog` atau `log`:

```bash
QUEUE_CONNECTION=database
```

## Kalimat Lanjutan

Routes: tidak perlu tambahan route karena email dikirim langsung dari Controller.

Testing:
- Manual test: kirim pesan kontak atau pendaftaran, cek log mail.
- Unit test: gunakan `Mail::fake()`.

### Catatan Penting

- Emails dilakukan async via Laravel Queue; config driver harus aktif (database/storage).
- Email template menggunakan layout `layout/emails/master.blade.php`.
- Hanya 2 mailable dikirim: `ContactFormReceivedMail` dan `RegistrationReceivedMail`.