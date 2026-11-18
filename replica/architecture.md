# Arsitektur & Rencana: Tapak Suci Sport Academy (REMAJA)

Dokumen ini adalah hasil analisis skema database dan kode aplikasi **apa adanya**
(as-is), lalu rencana perbaikan bertahap dan saran fitur. Fokusnya database dan
modul, bukan redesign tampilan.

- **Sumber analisis:** repo `main` @ `61f8098` + skema hasil `migrate:fresh` lokal.
- **Skala:** 29 tabel domain + 9 tabel framework (cache, jobs, sessions, dst).
- **Status:** Fase A, B, dan C sudah diterapkan (19-11-2026). UI admin dan driver WhatsApp nyata menyusul.

### Status penerapan

| item Fase A | status | catatan |
| --- | --- | --- |
| Tabel `branches` + `branch_id` | ✅ diterapkan | kolom string lama dipertahankan (transisi) |
| Pivot `program_branch` | ✅ diterapkan | backfill dari `programs.branches` |
| Unifikasi pelatih | ✅ aditif | `coaches.user_id`, `training_schedules.coach_ref_id` |
| Constraint pembayaran | ✅ diterapkan | `(student_id, payment_type_id, period)` |
| Dokumen siswa | ✅ tabel dibuat | belum ada UI/upload |
| Audit log | ✅ diterapkan | siswa, pembayaran, UKT, absensi |
| Zona waktu | ✅ diterapkan | simpan UTC, tampil `Asia/Jakarta` |
| Enkripsi NIK/KK | ⏸ ditunda | butuh migrasi data bertahap |

### Status Fase B

| item Fase B | status | catatan |
| --- | --- | --- |
| `enrollments` | ✅ diterapkan | backfill + auto-enroll siswa baru |
| `coach_student` | ✅ diterapkan | tabel + relasi |
| `event_participants` | ✅ diterapkan | unik per event+siswa |
| `notification_logs` | ✅ diterapkan | audit email/WhatsApp |
| Hapus kolom duplikat | ⏸ ditunda | butuh cutover UI |
| Aktivasi queue worker | ⏸ operasional | cron sudah didokumentasikan |

### Status Fase C

| item Fase C | status | catatan |
| --- | --- | --- |
| `guardians` + pivot | ✅ diterapkan | portal wali belum ada |
| `coach_rates` + `coach_payrolls` | ✅ diterapkan | kalkulator sesi dari absensi |
| `items` + `stock_movements` | ✅ diterapkan | stok update otomatis |
| QR check-in | ✅ skema + helper | endpoint/scan belum ada |
| WhatsApp gateway | ✅ abstraksi + driver log | driver provider nyata belum |
| UI admin Fase C | ⏸ belum | resource Filament menyusul |

---

## 1. Stack

| layer | pilihan saat ini | catatan |
| --- | --- | --- |
| web | Laravel 12.70 + Livewire 3 + Filament 5.10.3 | panel admin Filament, frontend Blade | 
| frontend | Blade + Bootstrap (turunan KickPro) + Vite | ada `public/css/custom.css`, `resources/css` |
| database | MySQL/MariaDB (produksi Hostinger), SQLite (lokal/CI) | `.env.example` memakai `DB_CONNECTION=sqlite` |
| auth | session Laravel + Filament; portal siswa login NIK + tanggal lahir | portal pembayaran terpisah, session sendiri |
| roles | `admin`, `operator`, `pelatih` (`UserRole`) | policy per resource + scoping pelatih |
| payments | Midtrans (`MidtransPaymentGateway`) + transfer manual | `PaymentGatewayManager`, webhook `/pembayaran/callback` |
| email | SMTP, `MAIL_MAILER=log` di contoh env | newsletter via queued job |
| jobs | queue `database` | worker belum tentu aktif di server |
| hosting | Hostinger shared + deploy GitHub Actions via SSH | lihat `docs/DEPLOYMENT-HOSTINGER.md` |
| timezone | **UTC** (`config/app.php`) | pengguna di WIB/WITA/WIT — lihat risiko §6 |

---

## 2. Peta tabel (29 domain)

### 2.1 Master data
| tabel | kolom kunci | catatan |
| --- | --- | --- |
| `students` | `student_code` (unik), `nik` (unik), `program_id`, `branch`, `belt_level`, `email`, `photo` | soft delete |
| `student_registrations` | `status`, `program_id`, `branch`, data ortu | soft delete tidak ada |
| `programs` | `slug` (unik), `level`, `monthly_fee`, `branches` JSON, `age_min/max` | soft delete |
| `coaches` | `name`, `specialization`, `photo_path`, `is_active`, `sort_order` | **tidak terhubung ke jadwal/absensi** |
| `training_schedules` | `day_of_week`, `start_time`, `end_time`, `branch`, `coach_id`, `coach_name`, `program_id` | `coach_id` → `users`, `coach_name` denormalisasi |
| `users` | `role`, `email` | akun admin/operator/pelatih |

### 2.2 Operasional latihan
| tabel | kolom kunci | catatan |
| --- | --- | --- |
| `attendances` | `student_id`, `training_schedule_id`, `date`, `status`, `recorded_by` | **UNIQUE(student, schedule, date)** |
| `belt_promotions` | `student_id`, `exam_date`, `current_belt`, `target_belt`, `result`, `certificate_number` (unik) | UKT |
| `events` | `title`, `starts_at`, `ends_at`, `type`, `is_public` | tidak ada peserta event |
| `achievements` | `student_id`, `competition_name`, `medal`, `event_date`, `is_published` | prestasi atlet |

### 2.3 Kebugaran (REMAJA)
| tabel | kolom kunci |
| --- | --- |
| `user_performances` | `student_id`, `test_date`, ~14 metrik (jantung, antropometri, kelincahan, VO2max), `custom_results` |
| `fitness_components` | `code` (unik), `name`, `weight` |
| `fitness_test_items` | `code`/`key` (unik), `fitness_component_id`, `unit`, `direction` |
| `fitness_benchmarks` | `fitness_test_item_id`, `gender`, `age_min`, `age_max`, `target_value` |
| `score_categories` | `min_score`, `max_score`, `label`, `color` |

### 2.4 Keuangan
| tabel | kolom kunci | catatan |
| --- | --- | --- |
| `payments` | `invoice_number` (unik), `student_id`, `payment_type_id`, `period`, `amount`, `status`, `due_date`, `paid_at`, `gateway_*` | **UNIQUE(student, period)** |
| `payment_types` | `slug` (unik), `default_amount`, `period_type` | |
| `payment_status_histories` | `payment_id`, `from_status`, `to_status`, `changed_by` | satu-satunya audit trail |

### 2.5 Konten & komunikasi
| tabel | catatan |
| --- | --- |
| `posts`, `categories`, `tags`, `post_tag` | blog; `posts.category` masih ada di samping `category_id` |
| `galleries`, `testimonials`, `faqs`, `contact_messages` | soft delete |
| `newsletters`, `newsletter_deliveries` | delivery unik per (newsletter,email) |
| `site_settings` | satu baris JSON |
| `notifications` | notifikasi database Filament |

---

## 3. Kekuatan skema (pertahankan)

1. **Idempotensi absensi:** `UNIQUE(student_id, training_schedule_id, date)` mencegah
   absen ganda per sesi.
2. **Idempotensi tagihan:** `invoice_number` unik dan `UNIQUE(student_id, period)`
   mencegah tagihan dobel — bagus untuk iuran bulanan.
3. **Idempotensi newsletter:** `UNIQUE(newsletter_id, email)` aman saat job retry.
4. **Audit pembayaran:** `payment_status_histories` mencatat transisi status.
5. **Kebugaran dinamis:** komponen/item/benchmark tidak hardcode di kode.
6. **Soft delete** pada entitas konten, dengan policy & scoping role.
7. **Index** pada kolom filter yang sering dipakai (`status`, `published_at`, `date`).

---

## 4. Temuan skema — yang perlu diperbaiki

Severity: **P0** = risiko data/uang/keamanan, **P1** = menghambat operasional,
**P2** = kualitas/pertumbuhan.

### P0-1. Identitas pelatih ganda (`users` vs `coaches`)
`training_schedules.coach_id` → `users.id`, sedangkan `coaches` adalah tabel profil
publik yang **tidak punya relasi** ke jadwal, absensi, atau rapor. Akibatnya:
- Mengubah profil pelatih tidak memengaruhi jadwal.
- Menghapus user pelatih memutus jadwal (FK tanpa `onDelete` eksplisit).
- Tidak bisa menjawab "atlet binaan pelatih X" dari data.

**Perbaikan:** jadikan `coaches` sumber identitas pelatih dan tambahkan
`coaches.user_id` (unik, nullable untuk pelatih non-akun), lalu `coach_id` mengarah
ke `coaches`. Hapus `coach_name` setelah migrasi data.

### P0-2. `payments` UNIQUE(student, period) terlalu ketat
Tidak bisa membuat dua tagihan untuk siswa yang sama di periode yang sama meski
jenisnya berbeda (mis. SPP + ujian UKT + seragam di bulan yang sama). Saat ini
tertutup hanya karena praktik pemakaian.

**Perbaikan:** ganti menjadi `UNIQUE(student_id, payment_type_id, period)`
(`period` nullable untuk tagihan sekali bayar → gunakan `period_key` yang
dinormalisasi agar NULL tidak membuat constraint bolong).

### P0-3. Data pribadi siswa tidak dilindungi
`students.nik` dan `students.family_card_number` (KK) tersimpan plaintext, tanpa
tabel dokumen dan tanpa retensi. Ini risiko UU PDP.

**Perbaikan:** enkripsi kolom (`casts` `encrypted`, atau enkripsi aplikasi),
buat `student_documents` (KK, akta, ijazah, foto) dengan kontrol akses, dan atur
retensi + hak hapus.

### P0-4. Audit log hanya untuk pembayaran
Perubahan siswa, UKT, absensi, dan pengaturan situs tidak tercatat siapa/kapan/nilai
lama-baru.

**Perbaikan:** activity log umum (`activity_log`: subject, causer, properties JSON)
dipasang lewat trait pada model sensitif.

### P1-1. `branch` berupa varchar bebas di 4 tempat
`students.branch`, `training_schedules.branch`, `student_registrations.branch`,
`programs.branches` (JSON). Tidak ada tabel `branches`, jadi:
- typo/kapitalisasi membuat laporan cabang pecah,
- tidak ada FK, tidak ada biaya/pelatih per cabang,
- filter `?branch=` bergantung string persis.

**Perbaikan:** tabel `branches` + `branch_id` FK; `programs.branches` diganti pivot
`program_branch`.

### P1-2. Tidak ada entitas keanggotaan (`enrollments`)
`students.program_id` hanya menyimpan program aktif. Riwayat pindah program, status
aktif/cuti/berhenti, dan tanggal mulai/selesai hilang.

**Perbaikan:** `enrollments(student_id, program_id, branch_id, started_at, ended_at,
status)`, dengan satu baris aktif per siswa (partial unique index).

### P1-3. Tidak ada penugasan atlet → pelatih
Relasi hanya lewat jadwal/cabang. Rapor, absensi, dan UKT tidak bisa dibatasi per
pelatih secara langsung.

**Perbaikan:** `coach_student(coach_id, student_id, assigned_at)` atau turunkan dari
`enrollments.coach_id`.

### P1-4. Timezone aplikasi UTC
`config/app.php` = `UTC`, tanpa konversi. Pengguna di WIB/WITA/WIT:
- `events.starts_at` tampil 7 jam lebih awal (acara 19:00 WIB muncul 12:00),
- `payments.paid_at`, `published_at`, `sent_at` bergeser,
- pengingat `dailyAt('08:00')` berjalan 08:00 UTC = 15:00 WIB.

**Perbaikan:** set `APP_TIMEZONE=Asia/Jakarta` (atau zona cabang), simpan timestamp
di UTC (`timestamptz`/`datetime` UTC) lalu konversi di tampilan; jadwalkan reminder
dengan `timezone('Asia/Jakarta')`.

### P1-5. Kolom duplikat/denormalisasi
- `posts.category` (string) vs `posts.category_id` → pakai `category_id` saja.
- `training_schedules.coach_name` vs `coach_id`.
- `programs.branches` JSON vs tabel cabang.
- `students.belt_level` vs riwayat `belt_promotions` (perlu `current_belt` yang
  diturunkan dari UKT terakhir, bukan diedit manual).

### P2-1. Batasan integritas yang belum ada
- `score_categories`: tidak ada cek rentang tidak tumpang tindih.
- `fitness_benchmarks`: index sebaiknya `(fitness_test_item_id, gender, age_min, age_max)`.
- `belt_promotions`: tidak terhubung ke `training_schedules`/cabang/event UKT.
- `achievements`: `competition_name` teks bebas, tidak terhubung ke `events`.
- `newsletter_deliveries`: belum menyimpan status kirim/bounce.
- `student_registrations`: tidak ada soft delete padahal ada alur review/hapus.
- `attendances.recorded_by` NOT NULL tanpa `onDelete('restrict')` eksplisit.

---

## 5. API / route

| method path | fungsi | akses | input | output | flow |
| --- | --- | --- | --- | --- | --- |
| GET `/` | beranda dinamis | publik | — | HTML | konten |
| GET `/program`, `/program/{slug}` | daftar/detail program | publik | filter branch | HTML | program |
| GET `/jadwal` | jadwal latihan | publik | — | HTML | latihan |
| GET/POST `/pendaftaran` | form pendaftaran | publik, throttle 5/menit | data calon | redirect | pendaftaran |
| GET/POST `/contact-us` | kontak | publik, throttle 5/menit | name/email/message | redirect | kontak |
| GET/POST `/testimoni` | testimoni | publik, throttle 5/menit | rating/konten | redirect | moderasi |
| GET `/blog`, `/blog/{slug}` | artikel | publik | category/tag | HTML | konten |
| GET `/gallery`, `/faq`, `/kalender`, `/prestasi` | konten | publik | bulan | HTML | konten |
| GET/POST `/portal-siswa` | login portal siswa | publik, throttle 10/menit | NIK + tgl lahir | session | portal |
| GET `/portal-siswa/dashboard` | rapor, absensi, UKT, prestasi | siswa login | — | HTML | portal |
| GET `/portal-siswa/rapor-pdf` | unduh rapor | siswa login | — | PDF | portal |
| GET/POST `/pembayaran*` | portal tagihan & checkout | publik, throttle 10/menit | NIK + tgl lahir | HTML/redirect | keuangan |
| POST `/pembayaran/callback` | webhook gateway | tanpa CSRF, throttle 120/menit | payload gateway | JSON | keuangan |
| GET `/kwitansi/{payment}` | kwitansi | auth | — | PDF | keuangan |
| GET `/sertifikat-ukt/{beltPromotion}` | sertifikat | auth + policy | — | PDF | UKT |
| GET `/admin/reports/*` | ekspor laporan | auth | periode | CSV/XLSX/PDF | laporan |
| `/admin` | panel Filament | role panel | — | HTML | admin |

**Webhook masuk:** Midtrans (`/pembayaran/callback`).
**Job terjadwal** (`routes/console.php`):
| perintah | jadwal | fungsi |
| --- | --- | --- |
| `payments:send-reminders --days=3` | 08:00 | pengingat tagihan |
| `db:backup` | 02:00 | backup + checksum |
| `queue:prune-failed --hours=168` | 03:00 | bersihkan failed job |
| `site-settings:prune-media` | 03:15 | karantina media yatim |
| `queue:monitor default:100` | tiap 5 menit | alert antrean |

---

## 6. The parts that bite

- **Timezone:** semua timestamp UTC tanpa konversi; jadwal Indonesia bergeser 7 jam.
- **Idempotensi webhook:** callback gateway bisa datang dua kali → pastikan update
  status memakai transisi terverifikasi dan `gateway_reference` unik.
- **Race tagihan:** generator bulanan + tombol manual bisa membuat tagihan ganda;
  `UNIQUE(student, period)` menutup ini, tapi akan hilang saat constraint diubah →
  ganti dengan `(student, payment_type, period_key)` sebelum melonggarkan.
- **Race absensi:** dua pelatih menyimpan sesi sama → unique index sudah menangani,
  tapi UI perlu pesan "sudah diabsen", bukan error 500.
- **PII:** NIK/KK plaintext; backup ikut membawa data sensitif ke disk.
- **Multi-cabang:** string bebas membuat laporan dan scoping tidak deterministik.
- **Queue worker:** reminder & newsletter mengandalkan queue; tanpa Supervisor,
  email menumpuk di `jobs` tanpa terkirim.
- **Backup:** `mysqldump` harus tersedia di server; restore wajib diuji berkala.

---

## 7. Rencana perbaikan bertahap

### Fase A — Integritas & keamanan (P0)
1. Migrasi `branches` + `branch_id` di siswa/jadwal/pendaftaran; backfill dari string
   unik yang ada.
2. Unifikasi identitas pelatih: `coaches.user_id`, pindahkan `coach_id` ke `coaches`,
   backfill, hapus `coach_name`.
3. Ganti constraint pembayaran: `UNIQUE(student_id, payment_type_id, period_key)`.
4. Enkripsi NIK/KK + tabel `student_documents` + policy akses + retensi.
5. Activity log pada `Student`, `Payment`, `BeltPromotion`, `Attendance`, `SiteSetting`.
6. Perbaiki timezone: `APP_TIMEZONE=Asia/Jakarta`, konversi tampilan, jadwal reminder
   berzona.

**Definisi selesai:** migrasi reversible + backfill teruji, test integritas baru
(dua tagihan beda jenis di periode sama boleh; duplikat ditolak), PII tidak terbaca
di DB, dan log perubahan bisa ditelusuri per record.

### Fase B — Operasional (P1)
1. `enrollments` + status keanggotaan (aktif/cuti/berhenti) dan riwayat program.
2. `coach_student` untuk scoping rapor/absensi per pelatih.
3. Hapus kolom duplikat (`posts.category`, `students.belt_level` manual) setelah
   view/backfill siap.
4. Aktifkan queue worker + monitoring kegagalan.
5. Event peserta: `event_participants` untuk lomba/ujian/pertemuan.

### Fase C — Pertumbuhan (P2)
1. Portal orang tua multi-anak + notifikasi.
2. WhatsApp gateway (pengingat SPP/jadwal/UKT) menggantikan email.
3. Payroll/honor pelatih.
4. Inventaris (seragam, alat).
5. Mobile/PWA + QR check-in.

---

## 8. Saran fitur (prioritas + dampak skema)

| fitur | nilai | dampak skema | prioritas |
| --- | --- | --- | --- |
| **QR check-in absensi** | pelatih absen cepat, kurangi salah input | `attendances.checkin_method`, `checked_in_at`; QR per siswa | P1 |
| **Portal orang tua multi-anak** | satu akun lihat semua anak | `guardians`, `guardian_student` | P1 |
| **WhatsApp reminder** | SPP & jadwal lebih sampai | `notification_logs(channel, target, status)` | P1 |
| **Payroll/honor pelatih** | hitung honor per sesi | `coach_rates`, `coach_payrolls` | P2 |
| **Inventaris seragam/alat** | stok & penjualan | `items`, `stock_movements`, `orders` | P2 |
| **Pendaftaran event/lomba** | peserta & biaya | `event_participants`, `event_fees` | P1 |
| **Rekam medis/catatan cedera** | keselamatan atlet | `medical_records` | P2 |
| **Leaderboard kebugaran & prestasi** | motivasi atlet | view dari `user_performances`/`achievements` | P2 |
| **Profil publik atlet** | SEO & prestasi | halaman publik + `achievements.is_published` | P2 |
| **Laporan per cabang/program** | keputusan pengurus | agregasi dari `branches`/`enrollments` | P1 |
| **Perpustakaan materi latihan (LMS)** | kurikulum UKT | `materials`, `material_progress` | P2 |
| **Sponsor/donasi** | pendanaan | `donations` | P2 |
| **Multi-tenant dojo** | replikasi ke cabang lain | `tenants`, `tenant_id` di semua tabel | P2 |

---

## 9. Langkah berikut

1. Terapkan Fase A di branch `refactor/schema-integrity`, satu migrasi per item.
2. Setiap migrasi: `up()` + `down()` + backfill + test.
3. Perbarui `docs/ARCHITECTURE.md` dan `docs/TODO.md` setelah tiap item selesai.
4. Jalankan `pint --test`, `composer audit`, `php artisan test`, lalu
   `migrate:fresh --seed` di CI.
5. Baru lanjut Fase B.

DDL usulan ada di `replica/schema-fixes.sql` (belum dijalankan).
