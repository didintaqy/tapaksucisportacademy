# TODO & Rencana Tindak Lanjut

Dokumen ini adalah daftar kerja operasional yang harus ditindaklanjuti lintas sesi.
Setiap item punya prioritas, bukti temuan, dan definisi selesai (acceptance criteria).

- **Terakhir diperbarui:** 21 Oktober 2026
- **Branch integrasi:** `develop`
- **Acuan strategis:** `docs/ROADMAP.md`
- **Status:** P0 selesai (RBAC + dependency audit); lanjut P1

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

---

## P1 — Kualitas & operasional

### P1.1 Standarisasi resource Filament

- [ ] Samakan `$navigationGroup`, `$navigationSort` berurutan 1..n per grup
- [ ] Samakan `$modelLabel` / `$pluralModelLabel` Bahasa Indonesia
- [ ] Perbaiki `UserResource`: group, label, `recordTitleAttribute = 'name'`
- [ ] Perbaiki label lowercase `student` / `student performance`
- [ ] Hilangkan tabrakan sort (Program vs FAQ)
- [ ] Seragamkan gaya label (`Newsletter / Broadcast`, `Sabuk / UKT`, `Blog & Artikel`)
- [ ] Tambahkan test label/navigation minimum

**Definisi selesai:** sidebar admin konsisten, tidak ada label Inggris/lowercase, urutan stabil.

### P1.2 Logging & error tracking

- [ ] Produksi: `LOG_CHANNEL=daily` (atau stack + daily), `LOG_LEVEL=warning`
- [ ] Tentukan retensi log (`LOG_DAILY_DAYS`)
- [ ] Pasang error tracker (Sentry/Flare) **atau** sepakati logs-only + alert
- [ ] Tambahkan exception reporting di `bootstrap/app.php` bila perlu
- [ ] Buat `docs/MONITORING.md`: log, error tracking, uptime, alert
- [ ] Tambahkan checklist logging ke `docs/DEPLOYMENT.md`
- [ ] Pantau endpoint `/up` (uptime monitor)

**Definisi selesai:** error produksi terlihat < 5 menit, ada prosedur alert, dokumentasi lengkap.

### P1.3 Queue & job berat

- [ ] Aktifkan queue worker produksi (Supervisor)
- [ ] Jalankan `php artisan queue:restart` setiap deploy (dokumentasikan)
- [ ] Pantau `failed_jobs` + alert
- [ ] Jadwalkan `queue:prune-failed --hours=168`
- [ ] Pindahkan pengiriman newsletter ke queued job/Mailable
- [ ] Test perilaku antrean (bukan hanya `Mail::fake`)

**Definisi selesai:** email & newsletter tidak dikirim sinkron; failed job terlihat dan bisa di-retry.

### P1.4 Audit responsivitas mobile

- [ ] Verifikasi halaman publik utama di 360px, 390px, 768px
- [ ] Perbaiki overflow/tabel/kalender
- [ ] Catat hasil di `docs/ROADMAP.md`

### P1.5 Scoping data Pelatih (row-level)

- [ ] Batasi Pelatih hanya melihat/mengubah absensi pada jadwal yang ia ampu
- [ ] Batasi Pelatih hanya melihat/mengubah UKT siswa yang terkait jadwalnya
- [ ] Tambahkan filter `getEloquentQuery()` + test akses lintas-jadwal
- [ ] Dokumentasikan hasilnya di matriks RBAC

**Catatan:** P0 baru menutup akses berbasis role (tanpa hapus). Scoping per-jadwal
adalah hardening tambahan agar Pelatih tidak bisa mengubah data jadwal lain.

---

## P2 — Hardening & kebersihan

### P2.1 CI hardening

- [ ] Tambahkan `migrate:fresh --seed` sebagai smoke test
- [ ] Tambahkan `php artisan config:cache && route:cache && view:cache`
- [ ] Tambahkan Composer cache + `concurrency` cancel-in-progress
- [ ] Pertimbangkan matrix PHP 8.2/8.3/8.4 (sesuai versi produksi)
- [x] Tambahkan `composer validate --strict` (sudah aktif di CI)

### P2.2 Sinkronisasi dokumentasi

- [ ] Perbarui matriks RBAC di `docs/ARCHITECTURE.md` (Program, Absensi, UKT, Newsletter, Kategori/Tag, Event, Pendaftaran)
- [ ] Perbarui tabel role di `README.md`
- [ ] Perbarui checklist `docs/DEPLOYMENT.md` (logging, monitoring, queue)
- [ ] Tautkan `docs/TODO.md` dari `docs/ROADMAP.md`

### P2.3 Fitur tertunda

- [ ] Reminder tagihan via WhatsApp (butuh provider WhatsApp Business API)
- [ ] Halaman `/program`: ganti section testimoni/FAQ statis dengan data dinamis atau hapus
- [ ] Audit link mati lain di header/footer template
- [ ] Evaluasi queue untuk export laporan besar

---

## Keputusan

| Topik | Keputusan | Tanggal | Catatan |
|---|---|---|---|
| Akses Absensi | Pelatih: lihat + catat/ubah; tanpa hapus | 21-10-2026 | Admin/Operator dapat hapus |
| Akses UKT | Pelatih: lihat + catat/ubah; tanpa hapus | 21-10-2026 | Admin/Operator dapat hapus |
| Akses Newsletter | Admin/Operator saja | 21-10-2026 | Aksi kirim butuh ability `send` |
| Error tracking | **Belum diputuskan** | — | Sentry vs Flare vs logs-only |
| Versi PHP produksi | **Belum diputuskan** | — | Menentukan matrix CI |

---

## Riwayat

- **21-10-2026** — Dokumen dibuat dari evaluasi Fase 0.
- **21-10-2026** — P0 selesai: 5 policy baru + `PolicyCoverageTest`; aksi kirim newsletter ber-ability `send`; dependency upgrade (Filament 5.10.3, Laravel 12.70.0, CommonMark 2.10.4, Flysystem 3.36.0, Symfony 7.4.21); `composer audit` bersih; audit ditambahkan ke CI; `composer.json` memakai platform PHP 8.2.0 agar lock file tetap kompatibel dengan CI.
