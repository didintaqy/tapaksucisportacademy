# Roadmap Pengembangan Tapak Suci Sport Academy

Dokumen ini adalah acuan resmi pengembangan website dari kondisi saat ini sampai go-live dan pemeliharaan. Setiap fase harus selesai, lulus test, dan bisa di-deploy sebelum lanjut ke fase berikutnya.

- **Stack:** Laravel 12.67, PHP 8.2, Filament 5, Blade + Vite, SQLite (development)
- **Repo:** `tapaksuciacademy`
- **Terakhir diperbarui:** 20 Oktober 2026

---

## 1. Status Modul

| Modul | Frontend | Admin | Status |
|---|---|---|---|
| Home, About, Program | ✅ | — | Selesai |
| Galeri kegiatan | ✅ | ✅ | Selesai |
| Blog & artikel | ✅ | ✅ | Selesai |
| Kontak (form → database) | ✅ | ✅ | Selesai |
| Data siswa | — | ✅ | Selesai |
| Performa fisik siswa | — | ✅ | Selesai (input manual) |
| Pembayaran & tagihan | ✅ portal | ✅ | Selesai |
| Generate tagihan bulanan | — | ✅ | Selesai |
| Dashboard admin | — | ✅ | Selesai |
| Pendaftaran siswa online | ✅ | ✅ | Selesai |
| Testimoni, FAQ, jadwal | ✅ | ✅ | Selesai |
| Absensi / kehadiran | ❌ | ❌ | Belum |
| Role & permission | — | ✅ | Selesai |
| Notifikasi email | ❌ | ❌ | Belum |
| SEO & analytics | ❌ | — | Belum |
| Deployment & backup | — | — | Dokumen siap, belum dieksekusi |

---

## 2. Prinsip Pengembangan

1. **Satu fase = satu milestone.** Jangan mulai fase baru sebelum definisi selesai fase sebelumnya terpenuhi.
2. **Tidak ada fitur tanpa:** migration, validasi, test, dan pembaruan dokumen ini.
3. **Data siswa dan keuangan adalah data sensitif.** Authorization (role + policy) wajib ada sebelum menambah fitur baru.
4. **Konfigurasi lewat `.env`.** Jangan hardcode kredensial, nominal default, atau kontak.
5. **Perubahan kecil dan terukur.** Commit per fitur, bukan per hari.

---

## 3. Fase 0 — Fondasi (prioritas sekarang)

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
- [x] Batasi `UserResource` hanya untuk `admin`
- [x] Rate limit + honeypot pada form kontak untuk mencegah spam
- [x] Password akun seeder diambil dari `.env`, bukan hardcode
- [x] Audit controller yang tidak terpakai (mis. `StudentController` frontend)

### 0.3 Kualitas Kode

- [x] Jalankan `vendor/bin/pint` dan commit hasilnya
- [ ] Samakan gaya penamaan dan struktur resource Filament
- [x] Pindahkan validasi form kontak ke Form Request
- [x] Hapus kode mati dan komentar yang tidak relevan

### 0.4 Database & Seeder

- [x] Jadikan semua seeder idempotent (`firstOrCreate` / `updateOrCreate`)
- [x] Pastikan `DatabaseSeeder` memanggil seeder dengan urutan benar
- [x] Lengkapi factory untuk semua model
- [x] Tentukan strategi backup database (lihat `docs/DEPLOYMENT.md`)

### 0.5 Testing & CI

- [x] Baseline seluruh test hijau (saat ini 72 test)
- [x] Test untuk modul kontak (submit, validasi, ubah status)
- [x] Tambah test untuk generate tagihan bulanan dan policy akses
- [x] Buat GitHub Actions: `vendor/bin/pint --test` + `php artisan test`
- [ ] Tetapkan target coverage minimal untuk modul keuangan

### 0.6 Operasional

- [x] Dokumentasikan deployment (server, `storage:link`, cron, queue)
- [ ] Aktifkan queue untuk email dan job berat
- [ ] Siapkan logging dan error tracking
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
- [ ] Notifikasi email saat ada pesan kontak atau pendaftaran baru
- [ ] SEO: meta title/description per halaman, sitemap.xml, Open Graph
- [ ] Analytics (mis. Umami/GA4) dan halaman 404 kustom
- [ ] Audit responsivitas dan aksesibilitas halaman publik

---

## 5. Fase 2 — Operasional Akademi

Tujuan: admin bisa mengelola kegiatan harian, bukan hanya data master.

- [ ] Absensi siswa per sesi latihan
- [ ] Riwayat sabuk dan ujian kenaikan tingkat (UKT)
- [ ] Rapor performa siswa (cetak PDF)
- [ ] Kalender kegiatan dan pengumuman
- [ ] Relasi pelatih ↔ kelas/siswa

---

## 6. Fase 3 — Keuangan

Tujuan: pengelolaan keuangan lengkap dan dapat diaudit.

- [ ] Rekap pemasukan per periode dan per jenis pembayaran
- [ ] Export laporan ke Excel/PDF
- [ ] Reminder tagihan otomatis (email/WhatsApp)
- [ ] Rekonsiliasi pembayaran gateway
- [ ] Laporan tunggakan per siswa
- [ ] Riwayat perubahan status pembayaran (audit trail)

---

## 7. Fase 4 — Konten & Komunikasi

- [ ] Newsletter / broadcast
- [ ] Multi-penulis dan moderasi artikel
- [ ] Kategori dan tag artikel
- [ ] Notifikasi internal untuk admin

---

## 8. Fase 5 — Go-Live & Pemeliharaan

- [ ] UAT bersama pengelola akademi
- [ ] Training operator (dokumen + sesi)
- [ ] Hardening: HTTPS, header keamanan, rate limit, permission file
- [ ] Backup otomatis + uji restore
- [ ] Monitoring uptime dan error
- [ ] Rencana pemeliharaan bulanan

---

## 9. Keputusan yang Menunggu

| Topik | Pilihan | Dampak |
|---|---|---|
| Role & permission | ✅ Enum `App\Enums\UserRole` + Policy | Cukup untuk 3 role saat ini; migrasi ke Spatie jika role bertambah banyak |
| Payment gateway | Manual vs Midtrans vs Xendit | Menentukan alur checkout dan rekonsiliasi |
| Email provider | SMTP biasa vs layanan transaksional | Memengaruhi pengiriman notifikasi |
| Hosting | VPS vs shared hosting | Menentukan proses deploy dan cron |
| Database produksi | SQLite vs MySQL/MariaDB | SQLite mudah, MySQL lebih aman untuk multi-user |

---

## 10. Cara Menggunakan Dokumen Ini

1. Pilih satu fase aktif. Fase 0 adalah fokus saat ini.
2. Pecah item checklist menjadi issue/task kecil.
3. Setiap task selesai: centang di dokumen ini dan sertakan test.
4. Akhir fase: review bersama pemilik produk sebelum lanjut.
5. Perbarui bagian "Status Modul" setiap ada fitur baru.
