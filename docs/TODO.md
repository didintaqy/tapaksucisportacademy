# TODO & Rencana Tindak Lanjut

Dokumen ini adalah daftar kerja operasional yang harus ditindaklanjuti lintas sesi.
Setiap item punya prioritas, bukti temuan, dan definisi selesai (acceptance criteria).

- **Terakhir diperbarui:** 25 Oktober 2026 (milestone data nyata selesai; fokus berikutnya Fase 5 — Go-Live & Pemeliharaan)
- **Branch integrasi:** `main`
- **Acuan strategis:** `docs/ROADMAP.md`
- **Status:** P0 & P1 selesai kecuali item operasional server (uptime monitor, queue worker, alert `failed_jobs`) dan audit mobile; lanjut Fase 5

## Cara pakai

1. Kerjakan dari prioritas **P0 → P1 → P2**.
2. Satu item = satu commit (atau satu PR kecil).
3. Centang item hanya jika test/lint lulus dan dokumentasi terkait diperbarui.
4. Jika keputusan bisnis berubah, catat di bagian **Keputusan** sebelum mengubah policy.

---

## P0 — Sebelum go-live (wajib)

### P0.1 RBAC: tutup akses konten & newsletter untuk Pelatih

- [x] Buat `CategoryPolicy` (Admin/Operator; delete Admin/Operator; restore/force delete Admin)
- [x] Buat `TagPolicy` (Admin/Operator; delete Admin/Operator; restore/force delete Admin)
- [x] Buat `NewsletterPolicy` + ability `send` (Admin/Operator)
- [x] Tambahkan `->authorize('send')` pada aksi **Kirim** newsletter
- [x] Tambahkan guard opsional di `Newsletter::send(?User $actor)` (defense in depth)
- [x] Test: Pelatih ditolak dari `/admin/categories`, `/admin/tags`, `/admin/newsletters`
- [x] Test: ability `send` hanya Admin/Operator

**Bukti temuan:** audit HTTP menunjukkan Pelatih mendapat `200` pada ketiga resource;
`Newsletter::send()` mengirim sinkron ke semua penerima.

**Definisi selesai:** Pelatih mendapat `403`; Admin/Operator tetap bisa mengelola; test lulus.

### P0.2 RBAC: policy Absensi & UKT

- [x] Buat `AttendancePolicy`
- [x] Buat `BeltPromotionPolicy`
- [x] Keputusan: Pelatih boleh lihat + catat/ubah; tidak boleh hapus
- [x] Test: Pelatih boleh akses halaman, tidak boleh delete; Operator boleh delete
- [x] Dokumentasikan keputusan di `docs/ARCHITECTURE.md`

**Definisi selesai:** tidak ada resource Filament tanpa policy; matriks akses teruji.

### P0.3 RBAC: test cakupan policy

- [x] Buat `tests/Feature/PolicyCoverageTest.php`
- [x] Test gagal jika ada resource Filament baru tanpa policy

**Definisi selesai:** resource baru tanpa policy langsung menggagalkan CI.

### P0.4 Keamanan dependency + CI audit

- [x] Update `filament/filament` ke versi bebas advisory
- [x] Update `laravel/framework` ke ≥ 12.69
- [x] Update `league/commonmark` ke > 2.10.1
- [x] Update `league/flysystem`, `symfony/http-foundation`, `symfony/yaml`
- [x] Jalankan `composer audit` sampai bersih
- [x] Tambahkan langkah `composer audit` ke `.github/workflows/ci.yml`
- [x] Regression test penuh setelah update

**Bukti temuan:** 7 advisory / 6 paket, termasuk commonmark **high** (ReDoS).

**Definisi selesai:** `composer audit` bersih dan CI menolak advisory baru.

### P0.5 Data publik nyata & pengaturan situs

Acuan: `docs/MILESTONE-DATA-REAL.md`.

- [x] Tabel `site_settings` + service `SiteSettings`
- [x] Halaman Filament **Pengaturan Situs** (identitas, kontak, peta, sosial, hero, about, biaya, SEO, footer)
- [x] Tabel `coaches` + resource **Pelatih** + `CoachPolicy`
- [x] Hapus seluruh konten contoh di Blade (alamat, telepon, harga, rating, pelatih, sosial, tautan `.html`)
- [x] Konten publik dari database/pengaturan; section kosong disembunyikan
- [x] Statistik dan rating dihitung dari database
- [x] Peta hanya dirender jika embed Google Maps valid
- [x] SEO controller tidak ditimpa composer
- [x] `ProductionSeeder` menyertakan pengaturan awal tanpa konten demo
- [x] Test konten dinamis, empty state, akses admin, policy, SEO
- [x] Isi identitas resmi, alamat, telepon/WhatsApp, email, jam, link/embed peta, dan media sosial
- [x] Isi profil pelatih resmi
- [x] Verifikasi iuran program dan biaya pendaftaran
- [x] Unggah logo, favicon, foto hero, dan foto latar header
- [x] UAT visual halaman publik setelah data resmi diisi

**Definisi selesai:** tidak ada placeholder kontak/identitas yang terlihat di produksi; semua section publik memakai data resmi atau tersembunyi.

---

## P1 — Kualitas & operasional

### P1.1 Standarisasi resource Filament

- [x] Samakan `$navigationGroup`, `$navigationSort` berurutan 1..n per grup
- [x] Samakan `$modelLabel` / `$pluralModelLabel` Bahasa Indonesia
- [x] Perbaiki `UserResource`: group, label, `recordTitleAttribute = 'name'`
- [x] Perbaiki label lowercase `student` / `student performance`
- [x] Hilangkan tabrakan sort (Program vs FAQ)
- [x] Seragamkan gaya label (`Newsletter / Broadcast`, `Sabuk / UKT`, `Blog & Artikel`)
- [x] Tambahkan test label/navigation minimum (`FilamentNavigationTest`)

**Definisi selesai:** sidebar admin konsisten, tidak ada label Inggris/lowercase, urutan stabil.

### P1.2 Logging & error tracking

- [x] Produksi: `LOG_CHANNEL=stack` + `LOG_STACK=daily`, `LOG_LEVEL=warning`
- [x] Tentukan retensi log (`LOG_DAILY_DAYS=30`)
- [x] Sepakati logs-only + uptime alert (tanpa Sentry/Flare)
- [x] Exception reporting default Laravel + log sudah memadai
- [x] Buat `docs/MONITORING.md`: log, error tracking, uptime, alert
- [x] Tambahkan checklist logging ke `docs/DEPLOYMENT.md`
- [ ] Pasang uptime monitor eksternal ke `/up` (operasional server)
- [x] Panduan operator + checklist pelatihan (`docs/TRAINING-OPERATOR.md`)

**Definisi selesai:** error produksi terlihat < 5 menit, ada prosedur alert, dokumentasi lengkap.

### P1.3 Queue & job berat

- [ ] Aktifkan queue worker produksi (Supervisor) — operasional server
- [x] Jalankan `php artisan queue:restart` setiap deploy (dokumentasikan)
- [x] Pantau `failed_jobs` + alert — `queue:monitor default:100` terjadwal 5 menit, alert ke `MAIL_ALERT_ADDRESS`
- [x] Jadwalkan `queue:prune-failed --hours=168`
- [x] Pindahkan pengiriman newsletter ke queued job + Mailable
- [x] Test perilaku antrean (bukan hanya `Mail::fake`)

**Definisi selesai:** email & newsletter tidak dikirim sinkron; failed job terlihat dan bisa di-retry.

### P1.4 Audit responsivitas mobile

- [ ] Verifikasi visual halaman publik utama di 360px, 390px, 768px (butuh browser/device)
- [x] Perbaiki overflow tabel pembayaran dan kalender (`.table-wrap`, `.calendar-scroll`)
- [ ] Catat hasil verifikasi visual di `docs/ROADMAP.md`

### P1.5 Scoping data Pelatih (row-level)

- [x] Batasi Pelatih hanya melihat/mengubah absensi pada jadwal yang ia ampu
- [x] Batasi Pelatih hanya melihat/mengubah UKT siswa yang terkait jadwalnya
- [x] Tambahkan filter `getEloquentQuery()` + test akses lintas-jadwal
- [x] Dokumentasikan hasilnya di matriks RBAC

**Catatan:** P0 baru menutup akses berbasis role (tanpa hapus). Scoping per-jadwal
adalah hardening tambahan agar Pelatih tidak bisa mengubah data jadwal lain.

---

## P2 — Hardening & kebersihan

### P2.0 Hardening keamanan aplikasi

- [x] Middleware `SecurityHeaders`: CSP, HSTS (hanya HTTPS), `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, COOP
- [x] Whitelist sumber eksternal CSP di `config/security.php` (Google Maps iframe, analytics)
- [x] Rate limit halaman cari ID siswa (`throttle:10,1`) — cegah penebakan NID/NIK
- [x] Rate limit webhook payment gateway (`throttle:120,1`)
- [x] Konfirmasi rate limit login admin Filament (5 percobaan/menit) + test
- [x] **Keputusan bisnis:** portal pembayaran kini memverifikasi **ID/NIK + tanggal lahir** (faktor kedua). Pesan error disamakan agar tidak membocorkan apakah suatu NIK terdaftar. Rate limit 10 percobaan/menit per IP.
- [ ] Pertimbangkan OTP WhatsApp sebagai pengganti tanggal lahir bila data siswa sering tidak akurat (butuh provider WhatsApp Business API)

### P2.1 CI hardening

- [x] Tambahkan `migrate:fresh --seed` sebagai smoke test
- [x] Tambahkan `php artisan config:cache && route:cache && view:cache`
- [x] Tambahkan Composer cache + `concurrency` cancel-in-progress
- [x] Pertimbangkan matrix PHP 8.2/8.3/8.4 (sesuai versi produksi)
- [x] Tambahkan `composer validate --strict` (sudah aktif di CI)
- [x] Siapkan workflow deploy produksi Hostinger via SSH (`deploy.yml`)
- [x] Tambahkan `ProductionSeeder` (admin + jenis pembayaran, tanpa data demo)
- [x] Dokumentasikan setup Hostinger, cron, queue, dan rollback
- [x] Isi environment/secrets GitHub dan jalankan deploy pertama (operasional)
- [x] Perbaiki langkah deploy yang menjalankan `db:seed` tanpa `--class` (risiko data demo & reset kata sandi admin)

### P2.2 Sinkronisasi dokumentasi

- [x] Perbarui matriks RBAC di `docs/ARCHITECTURE.md` (Program, Absensi, UKT, Newsletter, Kategori/Tag, Event, Pendaftaran)
- [x] Perbarui tabel role di `README.md`
- [x] Perbarui checklist `docs/DEPLOYMENT.md` (logging, monitoring, queue)
- [x] Tautkan `docs/TODO.md` dari `docs/ROADMAP.md`

### P2.3 Fitur tertunda

- [x] Hapus `db:seed` dari langkah deploy; `db:seed` tanpa `--class` hanya menjalankan `ProductionSeeder` di produksi
- [x] Seeder demo tidak menimpa kata sandi akun dan tidak memakai kredensial/domain produksi
- [x] `SeederSafetyTest` mencegah regresi seeder di jalur deploy
- [ ] Reminder tagihan via WhatsApp (butuh provider WhatsApp Business API)
- [x] Halaman `/program`: section testimoni dan FAQ kini memakai data dinamis dari database (disembunyikan jika kosong)
- [x] Audit link mati lain di header/footer template
- [x] Evaluasi queue untuk export laporan besar (`ExportFinancialReportJob` + notifikasi unduhan)
- [x] Idempotensi pengiriman newsletter per penerima (`newsletter_deliveries`, hindari duplikat saat job retry)
- [x] Modul Prestasi & Kejuaraan Atlet (`Achievement`, resource Filament, halaman publik `/prestasi`, integrasi rapor PDF)
- [x] Analisis Performa & Benchmark Kebugaran Dinamis (REMAJA: `FitnessComponent`, `FitnessTestItem`, `FitnessBenchmark` usia/gender, `ScoreCategory`, `PerformanceAnalysisService`)
- [x] Portal Siswa Lengkap (`/portal-siswa`: profil kebugaran REMAJA, radar benchmark, grafik perkembangan antar sesi, kehadiran, prestasi, UKT/sertifikat, dan cetak rapor PDF)

### P2.4 Pemeliharaan

- [x] Rencana pemeliharaan bulanan (`docs/MAINTENANCE.md`): harian, mingguan, bulanan, triwulanan, log pemeliharaan
- [ ] Isi nama & kontak penanggung jawab teknis di `docs/MAINTENANCE.md` §7
- [ ] Jadwalkan sesi pelatihan operator pertama

---

## Keputusan

| Topik | Keputusan | Tanggal | Catatan |
|---|---|---|---|
| Akses Absensi | Pelatih: lihat + catat/ubah hanya jadwal sendiri; tanpa hapus | 21-10-2026 | Admin/Operator dapat hapus |
| Akses UKT | Pelatih: lihat + catat/ubah hanya cabang jadwalnya; tanpa hapus | 21-10-2026 | Admin/Operator dapat hapus |
| Akses Newsletter | Admin/Operator saja | 21-10-2026 | Aksi kirim butuh ability `send` |
| Portal pembayaran | **ID/NIK + tanggal lahir** | 26-10-2026 | Faktor kedua tanpa infrastruktur baru; OTP WhatsApp sebagai opsi lanjutan |
| Error tracking | **Logs-only + uptime alert** | 21-10-2026 | Tanpa Sentry/Flare; lihat `docs/MONITORING.md` |
| Versi PHP produksi | **Belum diputuskan** | — | Menentukan matrix CI |

---

## Riwayat

- **21-10-2026** — Dokumen dibuat dari evaluasi Fase 0.
- **21-10-2026** — P0 selesai: 5 policy baru + `PolicyCoverageTest`; aksi kirim newsletter ber-ability `send`; dependency upgrade (Filament 5.10.3, Laravel 12.70.0, CommonMark 2.10.4, Flysystem 3.36.0, Symfony 7.4.21); `composer audit` bersih; audit ditambahkan ke CI; `composer.json` memakai platform PHP 8.2.0 agar lock file tetap kompatibel dengan CI.
- **21-10-2026** — P1 selesai (kecuali aktivasi server): standarisasi navigasi/label Filament; logging `daily` + `docs/MONITORING.md`; newsletter dipindah ke `SendNewsletterJob` + `NewsletterMail`; scoping Pelatih per jadwal; perbaikan route sertifikat UKT dan otorisasi kwitansi; perbaikan overflow kalender/tabel pembayaran.
- **21-10-2026** — Deploy produksi Hostinger disiapkan: workflow GitHub Actions (`deploy.yml`), `ProductionSeeder`, dan panduan `docs/DEPLOYMENT-HOSTINGER.md` (SSH, shared `.env`/storage, cron queue, rollback).
- **25-10-2026** — Milestone Data Nyata & Pengaturan Situs selesai: `site_settings`, `coaches`, halaman admin, frontend dinamis, SEO composer, `SiteSettingSeeder`, 209 test lulus, `pint` lulus. Data resmi sudah diisi pengelola melalui panel admin produksi.
- **25-10-2026** — Sinkronisasi dokumen: `ROADMAP.md` dan `MILESTONE-DATA-REAL.md` disamakan dengan status TODO (versi stack, branch `main`, deploy pertama, keputusan hosting/email/MySQL).
- **26-10-2026** — Perbaikan keamanan deploy: `db:seed` dihapus dari workflow deploy; `DatabaseSeeder` hanya menjalankan `ProductionSeeder` di produksi/staging; `UsersTableSeeder` demo tidak lagi menimpa kata sandi, memakai kata sandi publik seragam, dan domain `@tapaksuci.test`; `SeederSafetyTest` ditambahkan (214 test lulus).
- **26-10-2026** — Backup & alert: `db:backup` diperkuat (gzip, checksum `.sha256`, `--no-compress`, retensi default 14 hari, `mysqldump` tanpa argumen `--password`), perintah baru `db:restore` (`--verify` + restore SQLite/MySQL dengan pengaman `--force`), backup harian 02:00 dengan log kegagalan, `queue:monitor default:100` terjadwal 5 menit, dokumentasi backup/restore di `DEPLOYMENT.md` dan `MONITORING.md`. Uji restore SQLite end-to-end berhasil (6 user/24 siswa pulih). 222 test lulus.
- **26-10-2026** — Hardening aplikasi: middleware `SecurityHeaders` (CSP, HSTS, nosniff, frame-ancestors, Permissions-Policy) dengan whitelist di `config/security.php`; rate limit cari ID siswa (10/menit) dan webhook gateway (120/menit); rate limit login Filament dikonfirmasi + diuji. 232 test lulus.
- **26-10-2026** — Portal pembayaran: verifikasi ID/NIK kini wajib disertai tanggal lahir (faktor kedua); pesan error diseragamkan agar tidak membocorkan keberadaan NIK; siswa tanpa tanggal lahir ditolak. `PaymentManagementTest` diperluas. 236 test lulus.
- **26-10-2026** — Dokumentasi operasional: `docs/TRAINING-OPERATOR.md` (panduan panel admin, rutinitas harian, aturan privasi, checklist pelatihan) dan `docs/MAINTENANCE.md` (jadwal harian–tahunan, log pemeliharaan, kontak darurat, eskalasi). README dan ROADMAP diperbarui.
- **18-11-2026** — Fase A (integritas data) diterapkan: tabel `branches` + `branch_id` dengan backfill otomatis, pivot `program_branch`, unifikasi identitas pelatih (`coaches.user_id`, `training_schedules.coach_ref_id`), constraint pembayaran `(student_id, payment_type_id, period)`, tabel `student_documents`, audit log `activity_log` (siswa, pembayaran, UKT, absensi), dan pemisahan zona waktu (simpan UTC, tampil `Asia/Jakarta`). 7 test baru di `SchemaPhaseATest`; 282 test lulus (2 test `DatabaseBackupTest` hanya jalan di Linux). Enkripsi NIK/KK sengaja ditunda karena butuh migrasi data.
- **18-11-2026** — Fase B (operasional) diterapkan: tabel `enrollments` (riwayat program + status aktif/cuti/berhenti/lulus, satu aktif per siswa lewat `active_key`), pivot `coach_student`, `event_participants` (peserta lomba/UKT), dan `notification_logs` (audit email/WhatsApp). Siswa baru dengan program otomatis mendapat keanggotaan aktif. 6 test baru di `SchemaPhaseBTest`. Penghapusan kolom duplikat dan aktivasi queue worker ditunda (butuh cutover UI dan akses server).
- **19-11-2026** — Fase C (pertumbuhan) diterapkan: `guardians` + `guardian_student` (wali multi-anak), tarif & payroll pelatih (`coach_rates`, `coach_payrolls`, kalkulator sesi dari absensi), inventaris (`items`, `stock_movements` dengan update stok otomatis), QR check-in (`students.qr_token`, `attendances.checked_in_at`/`checkin_method`), dan lapisan WhatsApp (`WhatsAppGateway` + driver `log`, konfigurasi `config/whatsapp.php`). 6 test baru di `SchemaPhaseCTest`. UI admin, driver WhatsApp nyata, dan portal wali belum dibuat.
- **19-11-2026** — UI admin Fase A–C: 7 resource Filament baru (Wali Siswa, Barang, Pergerakan Stok, Keanggotaan, Peserta Event, Tarif Pelatih, Honor Pelatih) dengan policy Admin/Operator dan hapus khusus Admin. Honor pelatih punya aksi **Hitung dari Absensi** dan **Tandai Dibayar**. 3 test baru di `FilamentPhaseCResourcesTest` (halaman list/create/edit terbuka untuk admin, ditolak untuk pelatih). Driver WhatsApp nyata, endpoint QR, dan portal wali belum dibuat.
- **19-11-2026** — Perbaikan upload foto menyeluruh: logo/favicon SVG & ICO kini diterima (sebelumnya ditolak aturan `image`), semua field foto diberi `visibility('public')` dan batas 5 MB, optimizer dipusatkan di `ImageOptimizer` dan melewati PDF/SVG/ICO, gambar Pengaturan Situs ikut dioptimasi (`optimizeAll`), serta `StorageFallbackController` menyajikan `/storage/{path}` bila symlink belum ada (`local.serve` dimatikan). 5 test baru di `ImageUploadTest`. 302 test lulus.
- **19-11-2026** — Perbaikan edit foto: `FileUpload` global memakai `fetchFileInformation(false)` sehingga path foto lama tidak dihapus dari form saat edit (penyebab foto hilang/NULL), preview mendukung aset tema `images/...` dan URL eksternal, dan `imageEditor()` diaktifkan untuk crop/rotate pada foto siswa, pelatih, program, artikel, serta galeri. Aset tema tidak lagi ikut dioptimasi. 2 test baru di `ImageEditTest`. 304 test lulus.
