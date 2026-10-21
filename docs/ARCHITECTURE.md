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
| Absensi | `Attendance` | `Attendances/` | — |
| UKT / sabuk | `BeltPromotion` | `BeltPromotions/` | — |
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

1. Siswa/orang tua mencari data di `/pembayaran` berdasarkan ID siswa atau NIK.
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
| Pelatih | Lihat | CRU (tanpa hapus) | CRU (tanpa hapus) | CRU (tanpa hapus) | Lihat & ubah jadwal sendiri | — | — |

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

## Proteksi Form Publik

Form kontak memakai tiga lapis perlindungan:

1. **Form Request** (`StoreContactMessageRequest`) untuk validasi.
2. **Honeypot** field `website` yang harus kosong.
3. **Rate limit** `throttle:5,1` pada route `POST /contact-us`.
