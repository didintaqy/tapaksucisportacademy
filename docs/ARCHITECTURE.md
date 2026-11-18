# Arsitektur Aplikasi

Dokumen ini menjelaskan struktur modul dan alur utama agar pengembang berikutnya tidak perlu menebak.

## Struktur Direktori

```
app/
├── Console/Commands/   # Command terjadwal (reminder, rekonsiliasi)
├── Contracts/          # Interface gateway pembayaran
├── Enums/              # Status & tipe (PaymentStatus, BeltRank, UserRole, ...)
├── Filament/
│   ├── Pages/          # Halaman kustom (laporan, generate rapor, kalender admin)
│   ├── Resources/      # CRUD admin per modul
│   └── Widgets/        # Widget dashboard
├── Http/Controllers/   # Controller frontend, portal pembayaran, export laporan
├── Mail/               # Mailable (kontak, pendaftaran, reminder tagihan)
├── Models/             # Model Eloquent
├── Policies/           # Otorisasi per model
└── Services/
    ├── Payments/       # Logika gateway & generate tagihan
    └── Reports/        # Agregasi & export laporan keuangan
```

## Modul

| Modul | Model | Admin | Frontend |
|---|---|---|---|
| Siswa | `Student` | `Students/` | — |
| Performa | `StudentPerformance` (tabel `user_performances`) | `StudentPerformances/` | — |
| Pembayaran | `Payment` | `Payments/` | Portal pembayaran |
| Audit status pembayaran | `PaymentStatusHistory` | Tab riwayat di detail pembayaran | — |
| Jenis pembayaran | `PaymentType` | `PaymentTypes/` | — |
| Laporan keuangan | agregasi `Payment` | `Pages/IncomeReport`, `OutstandingReport` | — |
| Rekonsiliasi gateway | `Payment.gateway_payload` | `Pages/GatewayReconciliation` | — |
| Blog | `Post` | `Posts/` | `/blog` |
| Program latihan | `Program` | `Programs/` | `/program`, `/program/{slug}` |
| Kategori artikel | `Category` | `Categories/` | filter blog |
| Tag artikel | `Tag` | `Tags/` | filter blog |
| Newsletter / broadcast | `Newsletter` | `Newsletters/` | — |
| Notifikasi internal | `notifications` (Filament DB notifications) | Lonceng di topbar admin | — |
| Galeri | `Gallery` | `Galleries/` | `/gallery` |
| Kontak | `ContactMessage` | `ContactMessages/` | `/contact-us` |
| Testimoni | `Testimonial` | `Testimonials/` | `/testimoni` |
| FAQ | `Faq` | `Faqs/` | `/faq` |
| Jadwal latihan | `TrainingSchedule` | `TrainingSchedules/` | `/jadwal` |
| Pendaftaran siswa | `StudentRegistration` | `StudentRegistrations/` | `/pendaftaran` |
| Kegiatan | `Event` | `Events/` | `/kalender` |
| Prestasi atlet | `Achievement` | `Achievements/` | `/prestasi`, Portal Siswa |
| Benchmark kebugaran (REMAJA) | `FitnessBenchmark`, `FitnessComponent`, `FitnessTestItem`, `ScoreCategory` | `FitnessBenchmarks/` | Portal Siswa (`/portal-siswa`) |
| Absensi | `Attendance` | `Attendances/` | Portal Siswa |
| UKT / sabuk | `BeltPromotion` | `BeltPromotions/` | Portal Siswa |
| Pengguna | `User` | `Users/` | — |

## Alur Program

1. Admin/operator mengelola program di **Manajemen Web → Program Latihan** (`ProgramResource`).
2. Program menyimpan konten publik (deskripsi, gambar, usia, level, durasi, iuran, SEO) dan daftar cabang pada kolom JSON `branches`.
3. `/program` menampilkan program aktif dan dapat difilter per cabang; `/program/{slug}` menampilkan detail beserta jadwal terkait.
4. Program terhubung ke `TrainingSchedule.program_id`, `Student.program_id`, dan `StudentRegistration.program_id`.
5. Generator tagihan bulanan memakai `Program.monthly_fee` bila tersedia, lalu fallback ke `PaymentType.default_amount`.

## Alur Kontak

1. Pengunjung membuka `GET /contact-us`.
2. Form dikirim ke `POST /contact-us` (`ContactUsController::store`).
3. Validasi: `name`, `email`, `message` wajib; `phone` opsional.
4. Data disimpan ke `contact_messages` dengan status `unread`.
5. Admin melihat pesan di menu **Manajemen Web → Kontak Masuk**.
6. Admin menandai **Dibaca** (`markAsRead`) atau **Dibalas** (`markAsReplied`).
7. Balasan dilakukan lewat email eksternal; sistem hanya menyimpan status, bukan isi balasan.

Status: `unread` → `read` → `replied`.

## Alur Testimoni

1. Pengunjung mengirim testimoni lewat `POST /testimoni`.
2. Testimoni tersimpan dengan status `pending`.
3. Admin/operator memoderasi di **Manajemen Web → Testimoni**.
4. Aksi **Setujui** mengisi `published_at`; aksi **Tolak** menyembunyikan testimoni.
5. Halaman `/testimoni` hanya menampilkan status `approved`.

## Alur Artikel: Kategori, Tag, dan Moderasi

1. Artikel disimpan di `posts` dengan relasi `category_id` (satu kategori) dan pivot `post_tag` (banyak tag).
2. Kategori dan tag dikelola di **Manajemen Web → Kategori Artikel / Tag Artikel**; slug dibuat otomatis dari nama.
3. Status artikel: `draft` → `review` → `published`.
4. Operator hanya dapat membuat/mengubah draf dan mengajukan **Ajukan Review**; tombol **Terbitkan** hanya muncul untuk Administrator (`PostPolicy::updateStatus`).
5. Halaman publik `/blog` hanya menampilkan status `published` dan mendukung filter `?category=slug` serta `?tag=slug`.
6. Saat operator membuat draf, sistem mengirim notifikasi database ke semua admin.

## Alur Newsletter / Broadcast

1. Admin/operator menyusun newsletter di **Manajemen Web → Newsletter / Broadcast** (subjek + konten HTML).
2. Selama `is_draft` aktif, newsletter disimpan sebagai `draft` dan belum dikirim.
3. Aksi **Kirim** memanggil `Newsletter::send()`: mengumpulkan email unik dari `users` dan `students`, mengirim HTML via `Mail`, lalu mengisi `sent_at` dan `recipients_count`.
4. Pengiriman berjalan sinkron; pindahkan ke queued Mailable bila penerima bertambah besar.
5. Setelah terkirim, admin menerima notifikasi database.

## Notifikasi Internal Admin

- Menggunakan fitur **database notifications bawaan Filament** (tabel `notifications`, UUID + morph `notifiable`).
- Panel diaktifkan lewat `->databaseNotifications()` dan polling 30 detik; ikon lonceng muncul di topbar.
- Pemicu otomatis via model event `created`/`updated`:
  - `Post` draf oleh non-admin → artikel baru diajukan.
  - `ContactMessage` → pesan kontak baru.
  - `StudentRegistration` → pendaftaran siswa baru.
  - `Payment` berubah ke `Paid` → pembayaran diterima.
  - `Newsletter::send()` → newsletter terkirim.
- Penerima ditentukan per tipe: admin saja, atau admin + operator.

## Alur Pendaftaran Siswa

1. Calon siswa/orang tua mengisi `POST /pendaftaran`.
2. Data tersimpan di `student_registrations` dengan status `new`.
3. Admin/operator menindaklanjuti di **Manajemen Web → Pendaftaran Siswa**.
4. Status berubah `new` → `contacted` → `accepted`/`rejected`; `reviewed_by` dan `reviewed_at` terisi otomatis.
5. Setelah diterima, admin membuat data siswa di menu **Master Data → Siswa** (pendaftaran tidak otomatis membuat siswa karena NIK dan data lain belum dikumpulkan).

## Alur Pembayaran

1. Siswa/orang tua mencari data di `/pembayaran` dengan **ID siswa atau NIK + tanggal lahir**. Tanggal lahir adalah faktor kedua karena portal bersifat publik; pesan error diseragamkan agar keberadaan NIK tidak bisa ditebak. Route dibatasi `throttle:10,1`.
2. Data siswa disimpan di session (`payment_portal_student_id`).
3. `/pembayaran/tagihan` menampilkan tagihan milik siswa tersebut.
4. Checkout memanggil gateway melalui `PaymentGatewayManager`.
   - `manual`: status menjadi `pending` dengan payload QRIS placeholder.
   - `midtrans` / `xendit`: charge dibuat sesuai kredensial di `.env`.
5. Callback masuk ke `POST /pembayaran/callback` (`PaymentCallbackController`), tanpa middleware CSRF.
6. Callback memverifikasi signature, mencocokkan `order_id`, lalu memperbarui status.
7. Setiap perubahan status dicatat ke `payment_status_histories` beserta aktor, alasan, dan metadata gateway.

## Audit Trail Status Pembayaran

- Model: `App\Models\PaymentStatusHistory` (tabel `payment_status_histories`).
- Pemicu: event `updated` pada `Payment` saat atribut `status` berubah.
- Isi: `from_status`, `to_status`, `changed_by`, `reason`, `metadata` (JSON), `created_at`.
- Cara mengubah status:
  - `$payment->markAsPaid($method, $paidAt, $reason)`
  - `$payment->markAsPending($method, $reason)`
  - `$payment->changeStatus($status, $reason, $metadata)`
- Riwayat tampil di halaman detail pembayaran (bagian **Riwayat Perubahan Status**).

## Laporan Keuangan

- Service agregasi: `App\Services\Reports\FinancialReportService`
  - `incomeSummary()`, `incomeByType()`, `incomeByMonth()`
  - `outstandingSummary()`, `outstandingByStudent()`
- Halaman admin (grup **Keuangan**):
  - **Rekap Pemasukan** `/admin/income-report`
  - **Laporan Tunggakan** `/admin/outstanding-report`
  - **Rekonsiliasi Gateway** `/admin/gateway-reconciliation`
- Export:
  - Excel `.xlsx`: `App\Services\Reports\FinancialExcelExport` (multi-sheet, header berwarna, freeze pane).
  - CSV: streaming dari `FinancialReportController`.
  - PDF: DomPDF dengan view `resources/views/reports/*.pdf.blade.php`.
- Route export: `/admin/reports/income/{export,excel,pdf}` dan `/admin/reports/outstanding/{export,excel,pdf}`.
- Otorisasi: `Gate::authorize('viewAny', Payment::class)` → hanya Admin & Operator.

## Pengingat Tagihan Otomatis

- Mailable: `App\Mail\PaymentReminderMail` (queued) + view `emails/payment-reminder.blade.php`.
- Command: `php artisan payments:send-reminders --days=3 [--dry-run] [--limit=0]`.
- Scheduler: `routes/console.php` → setiap hari 08:00, `withoutOverlapping()->onOneServer()`.
- Seleksi: status outstanding, `due_date ≤ hari ini + N`, dan belum diingatkan hari ini (`reminded_at`).
- Setelah terkirim: `reminded_at` diisi dan `reminder_count` bertambah → mencegah duplikasi harian.
- Penerima: `students.email`. Siswa tanpa email dilewati dan dihitung pada output command.
- Pengingat manual juga tersedia sebagai aksi **Pengingat** (single & bulk) di tabel pembayaran.

## Rekonsiliasi Gateway

- Sumber kebenaran: `payments.gateway_payload.last_callback.transaction_status`.
- Mapping status gateway → lokal:
  - `settlement`/`capture` → `paid`
  - `pending` → `pending`
  - `deny` → `failed`
  - `cancel`/`refund` → `cancelled`
  - `expire` → `expired`
- Helper di `Payment`: `gatewayTransactionStatus()`, `expectedStatusFromGateway()`, `hasGatewayMismatch()`, `gatewayLastCallbackAt()`.
- Manual: halaman **Rekonsiliasi Gateway** (aksi per baris + **Selaraskan Semua**).
- CLI: `php artisan payments:reconcile-gateway [--apply] [--limit=0]`.
- Semua penyelarasan menulis audit trail dengan alasan `Rekonsiliasi manual…` atau `Rekonsiliasi otomatis…`.

## Generate Tagihan Bulanan

- Service: `app/Services/Payments/MonthlyBillGenerator.php`
- Tombol: **Generate Tagihan Bulanan** di halaman daftar pembayaran.
- Input: jenis pembayaran bertipe `monthly` + periode (`Y-m`).
- Perilaku:
  - Membuat satu tagihan per siswa untuk periode tersebut.
  - Nominal diambil dari `payment_types.default_amount`.
  - Jatuh tempo akhir bulan.
  - Siswa yang sudah punya tagihan pada periode itu dilewati.
- Pengaman tambahan: unique index `(student_id, period)` pada tabel `payments`.

## Dashboard Admin

Widget di `app/Filament/Widgets/` dan terdaftar otomatis lewat `discoverWidgets`:

| Widget | Isi |
|---|---|
| `DashboardStatsOverview` | Total siswa, pemasukan bulan ini, tunggakan, jatuh tempo ≤ 7 hari, berita draf, terbit bulan ini, galeri belum terbit |
| `IncomeChart` | Bar chart lunas vs belum lunas, 6 bulan terakhir |
| `DueBillsTable` | Tagihan outstanding yang jatuh tempo ≤ 7 hari atau tanpa tanggal |
| `PerformanceOverview` | Siswa tanpa data fisik, rata-rata VO2max, rata-rata push-up |

## Konvensi

- Semua nominal disimpan sebagai `decimal(12,2)` dan diformat dengan `number_format` / `money('IDR')`.
- Status pembayaran memakai enum `App\Enums\PaymentStatus`.
- Setiap resource admin menempatkan form di `Schemas/` dan tabel di `Tables/`.
- Periode bulanan memakai format `Y-m` (contoh `2026-10`).
- `students.student_code` dibuat otomatis dengan format `SIS-00001` setelah record tersimpan.
- `students.email` bersifat opsional dan dipakai sebagai tujuan pengingat tagihan.

## Role & Otorisasi

Role pengguna disimpan di `users.role` dan di-cast ke `App\Enums\UserRole`.

### Operasional & akademik

| Role | Siswa | Performa | Absensi | UKT / Sabuk | Jadwal | Pendaftaran | Kegiatan |
|---|---|---|---|---|---|---|---|
| Administrator | CRUD | CRUD | CRUD | CRUD | CRUD | CRUD | CRUD |
| Operator | CRU (tanpa hapus) | CRUD | CRUD | CRUD | CRU (tanpa hapus) | CRU (tanpa hapus) | CRU (tanpa hapus) |
| Pelatih | Lihat | CRU (tanpa hapus) | CRU (tanpa hapus)² | CRU (tanpa hapus)² | Lihat & ubah jadwal sendiri | — | — |

### Keuangan

| Role | Pembayaran | Laporan & Rekonsiliasi | Jenis Bayar |
|---|---|---|---|
| Administrator | CRUD | Lihat + export + rekonsiliasi | CRUD |
| Operator | CRU (tanpa hapus) | Lihat + export + rekonsiliasi | Lihat |
| Pelatih | — | — | — |

### Konten & web

| Role | Konten (Artikel/Galeri) | Kategori & Tag | Newsletter | Program | Pesan Kontak | Testimoni & FAQ | Pengguna |
|---|---|---|---|---|---|---|---|
| Administrator | CRUD | CRUD | CRUD + kirim | CRUD | CRUD | CRUD | CRUD |
| Operator | CRUD¹ | CRUD | CRU + kirim | CRU (tanpa hapus) | RU (tanpa hapus) | CRU (tanpa hapus) | — |
| Pelatih | — | — | — | — | — | — | — |

¹ Operator dapat membuat/mengubah artikel, tetapi hanya Administrator yang dapat mempublikasikan (status `Published`). Aksi **Kirim** newsletter memerlukan ability `send` pada `NewsletterPolicy`.
² Pelatih hanya melihat/mengubah absensi pada jadwal dengan `coach_id` miliknya, dan UKT siswa pada cabang yang sama dengan jadwal yang ia ampu. Filter diterapkan di policy dan `getEloquentQuery()` resource.

Policy berada di `app/Policies/` dan ditemukan otomatis oleh Laravel. Setiap model yang dipakai resource Filament wajib punya policy; `tests/Feature/PolicyCoverageTest.php` akan gagal jika ada resource baru tanpa policy. Widget dashboard yang menampilkan data keuangan disembunyikan untuk role Pelatih lewat `canView()`.

## Menambah Resource Filament

1. Buat resource:

```bash
php artisan make:filament-resource NamaModel
```

2. Tempatkan form di `Schemas/NamaModelForm.php` dan tabel di `Tables/NamaModelsTable.php`.
3. Set `$navigationGroup` (`Master Data`, `Keuangan`, atau `Manajemen Web`) dan `$navigationSort`.
4. Buat Policy di `app/Policies/NamaModelPolicy.php` dengan matriks role yang sama.
5. Tambahkan test policy di `tests/Feature/RolePermissionTest.php`.
6. Tambahkan factory dan seeder idempotent bila model butuh data awal.
7. Jalankan `vendor/bin/pint` dan `php artisan test`.

## Skema Fase A (integritas data)

Perubahan skema bertahap mulai 18-11-2026. Kolom lama **tidak dihapus** agar
aplikasi tetap berjalan selama transisi.

### Cabang (`branches`)

- Tabel `branches` (nama, slug, alamat, telepon, aktif, urutan) menjadi sumber
  data cabang.
- `students.branch_id`, `training_schedules.branch_id`, dan
  `student_registrations.branch_id` diisi otomatis dari kolom string `branch`
  lewat trait `SyncsBranch`.
- Pivot `program_branch` menggantikan pemakaian `programs.branches` (JSON).
- Relasi: `Student::branchRecord()`, `TrainingSchedule::branchRecord()`,
  `StudentRegistration::branchRecord()`, `Program::branchRecords()`.
- **Masih transisi:** kolom string `branch` dan `programs.branches` tetap ada.
  Hapus setelah semua tampilan/filter membaca relasi baru.

### Identitas pelatih

- `coaches.user_id` menautkan profil pelatih ke akun login (opsional).
- `training_schedules.coach_ref_id` menunjuk ke `coaches`; `coach_id` lama
  (ke `users`) dipertahankan sampai cutover.
- Relasi baru: `Coach::user()`, `Coach::schedules()`,
  `TrainingSchedule::coachProfile()`.
- Backfill mencocokkan nama pelatih dengan user ber-role `pelatih`; nama ganda
  dilewati dan perlu ditinjau admin.

### Pembayaran

- Unique berubah dari `(student_id, period)` menjadi
  `(student_id, payment_type_id, period)`.
- Efek: SPP, ujian, dan seragam boleh berada di periode yang sama; tagihan
  sekali bayar (`period` NULL) tidak dibatasi.
- Index baru `(status, paid_at)` untuk laporan pemasukan.

### Dokumen siswa

- Tabel `student_documents` untuk KK, akta, ijazah, dan foto.
- Relasi `Student::documents()`.
- **Belum diterapkan:** enkripsi kolom `nik` dan `family_card_number`. Data
  lama masih plaintext sehingga enkripsi butuh migrasi data tersendiri.

### Audit log

- Tabel `activity_log` mencatat `created`, `updated`, `deleted`, `restored`
  untuk `Student`, `Payment`, `BeltPromotion`, dan `Attendance` lewat trait
  `LogsActivity`.
- Kegagalan pencatatan tidak menggagalkan operasi utama.

### Zona waktu

- Timestamp tetap disimpan **UTC** (`config('app.timezone')`).
- `config('app.display_timezone')` (`APP_DISPLAY_TIMEZONE`, default
  `Asia/Jakarta`) dipakai untuk tampilan dan zona penjadwalan.
- `Event::localStartsAt()` / `localEndsAt()` mengonversi ke zona tampilan;
  kalender mengelompokkan event per hari lokal.
- Semua jadwal di `routes/console.php` memakai `->timezone(...)` agar berjalan
  pada jam lokal, bukan UTC.

## Skema Fase B (operasional)

Ditambahkan 18-11-2026. Semua aditif; belum ada kolom lama yang dihapus.

### Keanggotaan (`enrollments`)

- Riwayat program per siswa: `program_id`, `branch_id`, `coach_id`,
  `started_at`, `ended_at`, dan `status` (`active`, `cuti`, `berhenti`, `lulus`).
- `active_key` + unique index menegakkan **satu keanggotaan aktif per siswa**;
  nilainya NULL untuk baris non-aktif sehingga riwayat lama tetap boleh ada.
- Backfill dari `students.program_id` saat migrasi.
- Siswa baru yang langsung punya `program_id` otomatis mendapat keanggotaan
  aktif (`Student::created`).
- Relasi: `Student::enrollments()`, `Student::activeEnrollment()`,
  `Program::enrollments()`, `Branch::enrollments()`.

### Penugasan atlet ke pelatih (`coach_student`)

- Pivot `coach_id` ↔ `student_id` dengan `assigned_at` dan `released_at`.
- Memungkinkan scoping rapor/absensi/UKT per pelatih tanpa bergantung pada
  cabang atau jadwal.
- Relasi: `Coach::students()`, `Student::coaches()`.

### Peserta event (`event_participants`)

- Menyimpan peserta lomba/UKT/pertemuan: `status`, `fee_amount`, `result_note`.
- Unik per `(event_id, student_id)`.
- Relasi: `Event::participants()`, `Event::participantRecords()`,
  `Student::events()`, `Student::eventParticipations()`.

### Log notifikasi (`notification_logs`)

- Mencatat pengiriman keluar (email/WhatsApp/SMS): channel, penerima, template,
  status, referensi provider, dan error.
- Belum ada pengiriman WhatsApp; tabel disiapkan agar channel baru bisa
  dicatat tanpa ubah struktur.
- Pembungkus: `NotificationLog::markAsSent()` / `markAsFailed()`.

### Yang belum dikerjakan di Fase B

- **Penghapusan kolom duplikat** (`posts.category`, `students.belt_level`,
  `training_schedules.coach_name`, kolom string `branch`): ditunda sampai
  seluruh tampilan, filter, dan form membaca relasi baru.
- **Aktivasi queue worker**: konfigurasi cron sudah ada di
  `docs/DEPLOYMENT-HOSTINGER.md` §7; yang tersisa adalah aktivasi di server.

## Skema Fase C (pertumbuhan)

Ditambahkan 19-11-2026. Aditif; UI admin belum dibuat.

### Wali / orang tua (`guardians`, `guardian_student`)

- Satu wali bisa terhubung ke banyak siswa dan sebaliknya.
- Pivot menyimpan `relation` (ayah/ibu/wali) dan `is_primary`.
- `guardians.user_id` (opsional) untuk akun login wali di masa depan.
- Relasi: `Guardian::students()`, `Guardian::primaryStudent()`,
  `Student::guardians()`.

### Honor pelatih (`coach_rates`, `coach_payrolls`)

- `coach_rates`: tarif per pelatih (`per_session`/`per_month`), masa berlaku,
  dan status aktif.
- `coach_payrolls`: rekap per periode `YYYY-MM` (jumlah sesi, tarif, bonus,
  potongan, total, status draft/disetujui/dibayar), unik per pelatih+periode.
- `CoachPayrollCalculator::sessionsHeld()` menghitung sesi unik
  (jadwal, tanggal) dari absensi; `draftFor()` mengisi draf payroll dan tidak
  menimpa payroll yang sudah disetujui/dibayar.
- Relasi: `Coach::rates()`, `Coach::payrolls()`.

### Inventaris (`items`, `stock_movements`)

- `items`: kode unik, kategori (seragam/alat/lain), satuan, harga, stok,
  stok minimum.
- `stock_movements`: masuk/keluar/penyesuaian; `items.stock` diperbarui
  otomatis saat pergerakan dibuat.
- Relasi: `Item::movements()`; scope `lowStock()`.

### QR check-in

- `students.qr_token` unik, dibuat otomatis saat siswa dibuat (token lama
  di-backfill saat migrasi).
- `attendances.checked_in_at` + `checkin_method` (`manual`/`qr`);
  `Attendance::markCheckedIn()` menandai hadir sekaligus waktu check-in.

### WhatsApp

- Kontrak `WhatsAppGateway`, `WhatsAppManager`, dan driver `log` sebagai
  default (menulis ke log + `notification_logs`).
- Konfigurasi di `config/whatsapp.php`; driver `fonnte`/`wablas` sudah punya
  slot konfigurasi tetapi implementasinya belum dibuat.
- Binding di `AppServiceProvider`, jadi pemakaian: `app(WhatsAppGateway::class)`.

### Menu admin (Filament)

- **Master Data → Wali Siswa** (`GuardianResource`, pilih siswa langsung dari form).
- **Master Data → Barang & Inventaris** (`ItemResource`, penanda stok menipis).
- **Master Data → Pergerakan Stok** (`StockMovementResource`; daftar + catat, tanpa edit/hapus agar stok tetap konsisten).
- **Manajemen Akademi → Keanggotaan** (`EnrollmentResource`).
- **Manajemen Web → Peserta Event** (`EventParticipantResource`).
- **Keuangan → Tarif Pelatih** (`CoachRateResource`).
- **Keuangan → Honor Pelatih** (`CoachPayrollResource`, aksi **Hitung dari Absensi** dan **Tandai Dibayar**).

Akses: Admin & Operator dapat melihat/mengubah; hapus hanya Admin; Pelatih ditolak.

### Yang belum dikerjakan di Fase C

- Implementasi driver WhatsApp nyata (Fonnte/Wablas) + template pesan.
- Halaman/endpoint check-in QR untuk pelatih.
- Portal wali (login multi-anak) dan PWA.

## Proteksi Form Publik

Form kontak memakai tiga lapis perlindungan:

1. **Form Request** (`StoreContactMessageRequest`) untuk validasi.
2. **Honeypot** field `website` yang harus kosong.
3. **Rate limit** `throttle:5,1` pada route `POST /contact-us`.
