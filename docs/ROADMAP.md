# Roadmap Pengembangan Tapak Suci Sport Academy

Dokumen ini adalah acuan resmi pengembangan website dari kondisi saat ini sampai go-live dan pemeliharaan. Setiap fase harus selesai, lulus test, dan bisa di-deploy sebelum lanjut ke fase berikutnya.

- **Stack:** Laravel 12.70, PHP 8.2 (platform Composer), Filament 5.10.3, Blade + Vite, SQLite (development)
- **Repo:** `tapaksuciacademy`
- **Branch integrasi:** `main` (`develop` sudah di-merge)
- **Backlog harian:** `docs/TODO.md` — dokumen ini mengikuti status di TODO
- **Terakhir diperbarui:** 25 Oktober 2026 (disinkronkan dengan `docs/TODO.md`)

---

## 1. Status Modul

| Modul | Frontend | Admin | Status |
|---|---|---|---|
| Home, About | ✅ | — | Selesai (konten dinamis dari pengaturan situs) |
| Pengaturan situs | ✅ | ✅ | **Selesai** (identitas, kontak, peta, sosial, hero, about, biaya, SEO, footer) |
| Profil pelatih publik | ✅ | ✅ | **Selesai** (tabel `coaches`, section otomatis sembunyi jika kosong) |
| Program latihan | ✅ | ✅ | **Selesai** (per cabang, terhubung jadwal, pendaftaran, siswa, SPP) |
| Galeri kegiatan | ✅ | ✅ | Selesai |
| Blog & artikel | ✅ | ✅ | Selesai |
| Kontak (form → database) | ✅ | ✅ | Selesai |
| Data siswa | — | ✅ | Selesai |
| Performa fisik siswa | ✅ portal | ✅ | **Selesai** (input manual + benchmark usia/gender & skor kategori REMAJA) |
| Prestasi & medali atlet | ✅ | ✅ | **Selesai** (rekap medali, filter kejuaraan, integrasi portal & rapor PDF) |
| Portal siswa (REMAJA) | ✅ | — | **Selesai** (`/portal-siswa`: profil fisik, radar, perkembangan, absensi, UKT, prestasi, rapor PDF) |
| Pembayaran & tagihan | ✅ portal | ✅ | Selesai |
| Generate tagihan bulanan | — | ✅ | Selesai |
| Rekap & export laporan keuangan | — | ✅ | **Selesai** |
| Pengingat tagihan otomatis (email) | — | ✅ | **Selesai** |
| Rekonsiliasi payment gateway | — | ✅ | **Selesai** |
| Audit trail status pembayaran | — | ✅ | **Selesai** |
| Dashboard admin | — | ✅ | Selesai |
| Pendaftaran siswa online | ✅ | ✅ | Selesai |
| Testimoni, FAQ, jadwal | ✅ | ✅ | Selesai |
| Absensi / kehadiran | ❌ | ✅ | **Selesai** |
| Role & permission | — | ✅ | Selesai |
| Notifikasi email | ❌ | ✅ | **Selesai** |
| SEO & analytics | ✅ | — | Selesai |
| Kalender kegiatan | ✅ | ✅ | **Selesai** |
| Sabuk / UKT | — | ✅ | **Selesai** |
| Rapor performa (PDF) | — | ✅ | **Selesai** |
| Relasi pelatih ↔ kelas | — | ✅ | **Selesai** |
| Newsletter / broadcast | — | ✅ | **Selesai** |
| Kategori & tag artikel | ✅ | ✅ | **Selesai** |
| Multi-penulis & moderasi artikel | — | ✅ | **Selesai** |
| Notifikasi internal admin | — | ✅ | **Selesai** |
| Deployment & backup | — | — | **Pipeline siap & deploy pertama berjalan** (Hostinger + SSH); backup otomatis + uji restore belum |

---

## 2. Prinsip Pengembangan

1. **Satu fase = satu milestone.** Jangan mulai fase baru sebelum definisi selesai fase sebelumnya terpenuhi.
2. **Tidak ada fitur tanpa:** migration, validasi, test, dan pembaruan dokumen ini.
3. **Data siswa dan keuangan adalah data sensitif.** Authorization (role + policy) wajib ada sebelum menambah fitur baru.
4. **Konfigurasi lewat `.env`.** Jangan hardcode kredensial, nominal default, atau kontak.
5. **Perubahan kecil dan terukur.** Commit per fitur, bukan per hari.

---

## 3. Fase 0 — Fondasi (selesai; sisa item operasional ada di TODO)

**Tujuan:** aplikasi aman, konsisten, terdokumentasi, dan siap dikembangkan lebih lanjut oleh siapa pun.

### 0.1 Dokumentasi

- [x] Buat `docs/ROADMAP.md` (dokumen ini)
- [x] Perbarui `README.md` agar sesuai fitur dan route aktual
- [x] Buat `docs/ARCHITECTURE.md`: struktur modul, alur pembayaran, alur kontak
- [x] Dokumentasikan cara menambah resource Filament baru

### 0.2 Akses & Keamanan

- [x] Tambah kolom `role` pada tabel `users` (`admin`, `operator`, `pelatih`)
  - Keputusan: enum sederhana `App\Enums\UserRole` (tanpa paket tambahan)
- [x] `User::canAccessPanel()` hanya untuk role yang berhak
- [x] Buat Policy untuk `Student`, `Payment`, `ContactMessage`, `Post`, `Gallery`
- [x] Lengkapi Policy untuk `Category`, `Tag`, `Newsletter` (termasuk ability `send`), `Attendance`, dan `BeltPromotion`
- [x] Tambahkan `PolicyCoverageTest` agar resource Filament baru tanpa policy langsung gagal di CI
- [x] Batasi `UserResource` hanya untuk `admin`
- [x] Rate limit + honeypot pada form kontak untuk mencegah spam
- [x] Password akun seeder diambil dari `.env`, bukan hardcode
- [x] Audit controller yang tidak terpakai (mis. `StudentController` frontend)

### 0.3 Kualitas Kode

- [x] Jalankan `vendor/bin/pint` dan commit hasilnya
- [x] Samakan gaya penamaan dan struktur resource Filament (label, grup, sort, `recordTitleAttribute`)
- [x] Pindahkan validasi form kontak ke Form Request
- [x] Hapus kode mati dan komentar yang tidak relevan

### 0.4 Database & Seeder

- [x] Jadikan semua seeder idempotent (`firstOrCreate` / `updateOrCreate`)
- [x] Pastikan `DatabaseSeeder` memanggil seeder dengan urutan benar
- [x] Lengkapi factory untuk semua model
- [x] Tentukan strategi backup database (lihat `docs/DEPLOYMENT.md`)

### 0.5 Testing & CI

- [x] Baseline seluruh test hijau
- [x] Test untuk modul kontak (submit, validasi, ubah status)
- [x] Tambah test untuk generate tagihan bulanan dan policy akses
- [x] Buat GitHub Actions: `vendor/bin/pint --test` + `php artisan test`
- [x] Tambahkan `composer audit` ke CI dan pastikan seluruh advisory dependency sudah diperbaiki
- [x] Target pengujian modul keuangan: setiap alur kritis (tagihan, perubahan status, reminder, ekspor, rekonsiliasi) punya feature test

### 0.6 Operasional

- [x] Sekarang catatan deployment, backup config (mail/driver) disediakan di `docs/DEPLOYMENT.md`
- [ ] Aktifkan queue worker untuk email dan job berat (kode & dokumentasi siap; aktivasi Supervisor di server — TODO P1.3)
- [x] Siapkan logging dan error tracking (logs-only + uptime alert; lihat `docs/MONITORING.md`)
  - Sisa: pasang uptime monitor eksternal ke `/up` (TODO P1.2)
- [x] Terapkan alur git: branch per fitur, conventional commit, pull request

**Definisi selesai Fase 0:**

- Hanya user ber-role yang bisa masuk admin dan mengakses data sesuai perannya.
- `README.md` dan `docs/` akurat.
- `pint` dan seluruh test lulus di CI.
- Prosedur deploy dan backup tertulis dan sudah dicoba minimal sekali.

---

## 4. Fase 1 — Website Publik Lengkap

Tujuan: website publik siap menjadi wajah resmi akademi.

- [x] Testimoni: CRUD admin + tampil di frontend
- [x] FAQ: CRUD admin + halaman frontend
- [x] Jadwal latihan: CRUD admin + tampil per cabang/kelas
- [x] Pendaftaran siswa online: form publik → tersimpan sebagai calon siswa → admin follow up
- [x] Notifikasi email saat ada pesan kontak atau pendaftaran baru
- [x] SEO: meta title/description per halaman, sitemap.xml, Open Graph (metatags & sitemap)
- [x] Halaman 404 kustom (fallback ke template)
- [x] Analytics hook (Umami/GA4 snippet di layout)
- [ ] Audit responsivitas mobile (Bootstrap template, verifikasi manual 360px/390px/768px — TODO P1.4)

### 1.1 Data Nyata & Pengaturan Situs

Milestone lengkap: **`docs/MILESTONE-DATA-REAL.md`** — **selesai** (data resmi sudah diisi melalui panel admin produksi).

- [x] Tabel `site_settings` + service `SiteSettings` (default, cache, media, peta)
- [x] Halaman admin **Pengaturan Situs** (identitas, kontak, peta, sosial, hero, about, biaya, SEO, footer)
- [x] Tabel `coaches` + resource **Pelatih** + policy
- [x] Ganti seluruh konten statis di header, footer, beranda, tentang, program, kontak, pendaftaran, FAQ, jadwal, testimoni, blog, galeri, dan kalender
- [x] Hapus tautan `.html`, video demo, alamat/telepon/email/harga/rating/nama pelatih contoh
- [x] Sembunyikan section yang belum memiliki data nyata
- [x] Peta Google Maps hanya dirender jika embed valid
- [x] Statistik dan rating dihitung dari database, bukan angka statis
- [x] Perbaiki SEO composer agar tidak menimpa meta dari controller
- [x] ProductionSeeder menyertakan pengaturan situs tanpa konten demo
- [x] Test service, akses admin, policy, konten dinamis, empty state, dan SEO
- [x] Isi data resmi akademi melalui panel admin (identitas, kontak, pelatih, biaya, media) — milestone **selesai**
- [ ] UAT visual mobile 360px/390px/768px (TODO P1.4)

---

## 5. Fase 2 — Operasional Akademi

Tujuan: admin bisa mengelola kegiatan harian, bukan hanya data master.

- [x] Absensi siswa per sesi latihan
- [x] Riwayat sabuk dan ujian kenaikan tingkat (UKT)
- [x] Rapor performa siswa (cetak PDF)
- [x] Kalender kegiatan dan pengumuman
- [x] Relasi pelatih ↔ kelas/siswa

---

## 6. Fase 3 — Keuangan

Tujuan: pengelolaan keuangan lengkap dan dapat diaudit.

- [x] Rekap pemasukan per periode dan per jenis pembayaran (`/admin/income-report`)
- [x] Export laporan ke Excel (`.xlsx`), CSV, dan PDF (`/admin/reports/*`)
- [x] Reminder tagihan otomatis via email (`payments:send-reminders`, terjadwal harian)
- [x] Rekonsiliasi pembayaran gateway (`/admin/gateway-reconciliation`, `payments:reconcile-gateway`)
- [x] Laporan tunggakan per siswa (`/admin/outstanding-report`)
- [x] Riwayat perubahan status pembayaran / audit trail (`payment_status_histories`)
- [ ] Reminder tagihan via WhatsApp (butuh provider WhatsApp Business API)

**Definisi selesai Fase 3:**

- Admin dan operator dapat membuat rekap pemasukan dan tunggakan, lalu mengekspornya.
- Setiap perubahan status pembayaran tercatat lengkap dengan aktor dan alasannya.
- Tagihan yang mendekati/melewati jatuh tempo diingatkan otomatis tanpa duplikasi harian.
- Status pembayaran gateway dapat diselaraskan dan hasilnya tercatat di audit trail.

---

## 7. Fase 4 — Konten & Komunikasi

- [x] Newsletter / broadcast
- [x] Multi-penulis dan moderasi artikel
- [x] Kategori dan tag artikel
- [x] Notifikasi internal untuk admin
- [x] Program latihan: CRUD admin, halaman daftar + detail, filter cabang, integrasi jadwal/pendaftaran/siswa/SPP

---

## 8. Fase 5 — Go-Live & Pemeliharaan

- [x] Siapkan pipeline deploy otomatis Hostinger (GitHub Actions + SSH)
- [x] Isi secrets environment `production` dan jalankan deploy pertama
- [ ] UAT bersama pengelola akademi
- [x] Training operator (dokumen + sesi) — panduan: `docs/TRAINING-OPERATOR.md`; **sesi pelatihan belum dijadwalkan**
- [ ] Hardening: HTTPS, header keamanan, rate limit, permission file
- [x] Backup otomatis + uji restore — `db:backup` harian 02:00 (retensi 14 hari, checksum) + `db:restore --verify`; **sisa:** salin backup ke luar server & uji restore penuh ke database terpisah
- [x] Hardening aplikasi: header keamanan (CSP/HSTS/nosniff/frame-ancestors) + rate limit login admin, cari ID siswa, dan webhook
- [ ] Hardening server: HTTPS, permission file, blokir akses `.env`/`database` (hPanel + .htaccess)
- [ ] Monitoring uptime dan error (sisa: pasang uptime monitor eksternal ke `/up`; logging, retensi log, dan alert antrean `queue:monitor` sudah selesai)
- [x] Rencana pemeliharaan bulanan — `docs/MAINTENANCE.md` (harian, mingguan, bulanan, triwulanan)

---

## 9. Keputusan yang Menunggu

| Topik | Pilihan | Dampak |
|---|---|---|
| Role & permission | ✅ Enum `App\Enums\UserRole` + Policy | Cukup untuk 3 role saat ini; migrasi ke Spatie jika role bertambah banyak |
| Payment gateway | ✅ Midtrans (QRIS) | Driver di `app/Services/Payments`, rekonsiliasi via payload callback; driver lain dapat ditambah lewat kontrak `PaymentGateway` |
| Reminder WhatsApp | Belum diputuskan | Butuh provider WhatsApp Business API (biaya + verifikasi template pesan) — TODO P2.3 |
| Email provider | ✅ SMTP produksi terkonfigurasi | Notifikasi & reminder berjalan lewat queue + `SendNewsletterJob`/`NewsletterMail` |
| Hosting | ✅ Hostinger Premium (SSH + GitHub Actions) | Lihat `docs/DEPLOYMENT-HOSTINGER.md` |
| Database produksi | ✅ MySQL/MariaDB di Hostinger | Diwajibkan `DB_CONNECTION=mysql` di `shared/.env`; SQLite hanya untuk development |

---

## 10. Cara Menggunakan Dokumen Ini

1. Pilih satu fase aktif. Saat ini fokus pada **Fase 5 — Go-Live & Pemeliharaan** (UAT, training operator, hardening, backup/restore, monitoring); sisa item Fase 0–3 dikerjakan sebagai task pendamping. Milestone **Data Nyata & Pengaturan Situs** sudah selesai (`docs/MILESTONE-DATA-REAL.md`).
2. Daftar tindak lanjut operasional lintas sesi ada di **`docs/TODO.md`** (prioritas P0/P1/P2, keputusan, dan acceptance criteria). Gunakan dokumen itu sebagai backlog harian.
3. Pecah item checklist menjadi issue/task kecil.
4. Setiap task selesai: centang di dokumen ini dan sertakan test.
5. Akhir fase: review bersama pemilik produk sebelum lanjut.
6. Perbarui bagian "Status Modul" setiap ada fitur baru.
