# Arsitektur Aplikasi

Dokumen ini menjelaskan struktur modul dan alur utama agar pengembang berikutnya tidak perlu menebak.

## Struktur Direktori

```
app/
├── Contracts/          # Interface gateway pembayaran
├── Enums/              # Status & tipe (PaymentStatus, PaymentPeriodType, PostStatus, ...)
├── Filament/
│   ├── Resources/      # CRUD admin per modul
│   └── Widgets/        # Widget dashboard
├── Http/Controllers/   # Controller frontend & portal pembayaran
├── Models/             # Model Eloquent
└── Services/Payments/  # Logika gateway & generate tagihan
```

## Modul

| Modul | Model | Admin | Frontend |
|---|---|---|---|
| Siswa | `Student` | `Students/` | — |
| Performa | `StudentPerformance` (tabel `user_performances`) | `StudentPerformances/` | — |
| Pembayaran | `Payment` | `Payments/` | Portal pembayaran |
| Jenis pembayaran | `PaymentType` | `PaymentTypes/` | — |
| Blog | `Post` | `Posts/` | `/blog` |
| Galeri | `Gallery` | `Galleries/` | `/gallery` |
| Kontak | `ContactMessage` | `ContactMessages/` | `/contact-us` |
| Pengguna | `User` | `Users/` | — |

## Alur Kontak

1. Pengunjung membuka `GET /contact-us`.
2. Form dikirim ke `POST /contact-us` (`ContactUsController::store`).
3. Validasi: `name`, `email`, `message` wajib; `phone` opsional.
4. Data disimpan ke `contact_messages` dengan status `unread`.
5. Admin melihat pesan di menu **Manajemen Web → Kontak Masuk**.
6. Admin menandai **Dibaca** (`markAsRead`) atau **Dibalas** (`markAsReplied`).
7. Balasan dilakukan lewat email eksternal; sistem hanya menyimpan status, bukan isi balasan.

Status: `unread` → `read` → `replied`.

## Alur Pembayaran

1. Siswa/orang tua mencari data di `/pembayaran` berdasarkan ID siswa atau NIK.
2. Data siswa disimpan di session (`payment_portal_student_id`).
3. `/pembayaran/tagihan` menampilkan tagihan milik siswa tersebut.
4. Checkout memanggil gateway melalui `PaymentGatewayManager`.
   - `manual`: status menjadi `pending` dengan payload QRIS placeholder.
   - `midtrans` / `xendit`: charge dibuat sesuai kredensial di `.env`.
5. Callback masuk ke `POST /pembayaran/callback` (`PaymentCallbackController`), tanpa middleware CSRF.
6. Callback memverifikasi signature, mencocokkan `order_id`, lalu menandai pembayaran `paid`.

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

## Role & Otorisasi

Role pengguna disimpan di `users.role` dan di-cast ke `App\Enums\UserRole`.

| Role | Siswa | Performa | Pembayaran | Jenis Bayar | Konten | Pesan Kontak | Pengguna |
|---|---|---|---|---|---|---|---|
| Administrator | CRUD | CRUD | CRUD | CRUD | CRUD | CRUD | CRUD |
| Operator | CRUD | CRUD | CRU (tanpa hapus) | Lihat | CRUD | RU (tanpa hapus) | — |
| Pelatih | Lihat | CRUD | — | — | — | — | — |

Policy berada di `app/Policies/` dan ditemukan otomatis oleh Laravel. Widget dashboard yang menampilkan data keuangan disembunyikan untuk role Pelatih lewat `canView()`.

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
