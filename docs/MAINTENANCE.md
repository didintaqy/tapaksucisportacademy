# Rencana Pemeliharaan Bulanan

Jadwal perawatan rutin untuk menjaga situs aman, cepat, dan data aman. Setiap
tugas punya **pemilik**, **waktu**, dan **cara verifikasi**.

- **Penanggung jawab teknis:** (isi nama + kontak)
- **Penanggung jawab konten:** (isi nama + kontak)
- **Terakhir diperbarui:** 26 Oktober 2026

---

## 1. Ringkasan Jadwal

| Frekuensi | Tugas | Pemilik |
|---|---|---|
| Setiap hari | Cek alert, log error, antrean, backup terakhir | Teknis |
| Setiap minggu | Verifikasi backup, disk, uji restore ringan | Teknis |
| Setiap bulan | Update dependency, uji restore penuh, review akses | Teknis |
| Setiap bulan | Review konten, harga, jadwal, dan FAQ | Konten |
| Setiap 3 bulan | Uji restore penuh + review kebijakan privasi | Teknis + Manajemen |
| Setiap 6 bulan | Rotasi kata sandi admin, audit log akses | Manajemen |

---

## 2. Harian (± 10 menit)

| # | Tugas | Cara | Kriteria lulus |
|---|---|---|---|
| 1 | Situs hidup | Buka `/up` → 200 `OK` | Status 200 |
| 2 | Cek log error | `grep -E "\.(ERROR\|CRITICAL)" storage/logs/laravel-$(date +%F).log` | Tidak ada error berulang |
| 3 | Cek antrean | `php artisan queue:failed` | Kosong, atau sudah di-retry |
| 4 | Backup terakhir | `ls -lh storage/app/backups/ \| tail -3` | Ada file hari ini |

> Alert antrean dikirim otomatis lewat `queue:monitor default:100` ke
> `MAIL_ALERT_ADDRESS`. Jika alert tidak pernah masuk padahal email biasa
> berjalan, periksa konfigurasi cron.

---

## 3. Mingguan (± 20 menit)

| # | Tugas | Cara | Kriteria lulus |
|---|---|---|---|
| 1 | Verifikasi backup | `php artisan db:restore storage/app/backups/<file>.gz --verify` | `Checksum valid.` + `Isi backup valid` |
| 2 | Kapasitas disk | `df -h` dan `du -sh storage/app/backups storage/logs` | Disk < 80% |
| 3 | Ukuran database | `php artisan db:show` | Wajar, tidak melonjak aneh |
| 4 | Salin backup keluar server | Unduh file `.gz` + `.sha256` terbaru | Tersimpan di komputer/cloud |
| 5 | Cek failed job | `php artisan queue:failed` | Kosong |

> **Backup yang hanya ada di server tidak melindungi dari kegagalan server.**
> Tugas #4 adalah yang paling sering terlewat dan paling penting.

---

## 4. Bulanan (± 90 menit)

### 4.1 Keamanan & dependency

```bash
cd ~/domains/tapaksucisportacademy.com/laravel/current

# 1. Cek advisory keamanan
composer audit

# 2. Cek versi paket yang usang
composer outdated --direct
```

- Jika `composer audit` menemukan advisory: update **segera**, bukan bulan depan.
- Prosedur update: buat branch, update, `php artisan test`, `vendor/bin/pint --test`,
  lalu merge. Jangan update langsung di server.

### 4.2 Uji restore penuh

Ikuti prosedur **"Uji restore penuh"** di `docs/DEPLOYMENT.md`:

- Restore ke database terpisah (bukan produksi)
- Bandingkan jumlah baris `users`, `students`, `payments`
- Hapus database uji setelah selesai
- Catat hasilnya di tabel §6

### 4.3 Review akses

| # | Tugas | Kriteria |
|---|---|---|
| 1 | Daftar pengguna admin/operator masih aktif | Hapus/nonaktifkan yang sudah tidak bertugas |
| 2 | Tidak ada akun dengan kata sandi lemah | Ganti bila perlu |
| 3 | Pelatih masih mengampu jadwal yang benar | Sesuaikan jika ada mutasi |

### 4.4 Data & konten

| # | Tugas | Pemilik |
|---|---|---|
| 1 | Harga dan iuran di Pengaturan Situs masih benar | Konten |
| 2 | Jadwal latihan sesuai kondisi terbaru | Konten |
| 3 | Profil pelatih diperbarui | Konten |
| 4 | Artikel/blog tidak ada yang usang menyesatkan | Konten |
| 5 | FAQ masih relevan | Konten |
| 6 | Semua siswa punya tanggal lahir (syarat portal pembayaran) | Operator |

Verifikasi #6:

```sql
SELECT id, student_code, full_name FROM students WHERE birth_date IS NULL;
```

---

## 5. Triwulanan & Tahunan

### 3 bulan sekali

- [ ] Uji restore penuh (§4.2)
- [ ] Review kebijakan privasi dan data yang tampil di halaman publik
- [ ] Cek kapasitas hosting (disk, bandwidth, batas email harian hPanel)
- [ ] Perbarui dokumentasi bila ada fitur/perubahan alur

### 6 bulan sekali

- [ ] Rotasi kata sandi seluruh akun Administrator
- [ ] Review daftar pengguna dan peran
- [ ] Cek masa berlaku domain dan SSL
- [ ] Evaluasi kebutuhan fitur baru dari pengelola akademi

### Tahunan

- [ ] Review total biaya hosting, email, dan gateway
- [ ] Tinjau ulang keputusan teknologi di `docs/ROADMAP.md` §9
- [ ] Backup arsip penuh (database + upload + `.env`) disimpan permanen

---

## 6. Log Pemeliharaan

Isi setiap kali tugas dijalankan. Berguna saat investigasi insiden.

| Tanggal | Tugas | Hasil | Pelaksana | Catatan |
|---|---|---|---|---|
| | | | | |

---

## 7. Kontak Darurat

| Peran | Nama | Kontak | Untuk apa |
|---|---|---|---|
| Penanggung jawab teknis | | | Situs mati, data bermasalah |
| Administrator panel | | | Akses, akun, konten |
| Hostinger Support | — | hPanel → Support | Server, DNS, SSL, email |
| Gateway pembayaran | — | Dashboard Midtrans | Gangguan transaksi |

---

## 8. Eskalasi Insiden

1. **Kenali dampak** — apakah seluruh situs mati, atau hanya satu halaman?
2. **Hentikan perubahan** — jangan menekan tombol apa pun sebelum situasi jelas.
3. **Cek `/up`** — 200 berarti aplikasi hidup; lihat log untuk detail.
4. **Hubungi teknis** — sertakan waktu kejadian, halaman, dan pesan error.
5. **Rollback jika perlu** — prosedur di `docs/DEPLOYMENT-HOSTINGER.md` §8.
6. **Catat** penyebab dan pencegahan di §6.

Rujukan prosedur insiden: `docs/MONITORING.md` §6.
