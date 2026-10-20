# Panduan Kontribusi & Version Control

Dokumen ini mengatur alur kerja git agar riwayat perubahan rapi dan aman.

## Model Branch

| Branch | Fungsi | Aturan |
|---|---|---|
| `main` | Produksi | Selalu stabil dan siap deploy. Hanya menerima merge dari `develop` atau `hotfix/*`. Diberi tag rilis. |
| `develop` | Integrasi | Tempat penggabungan semua fitur. Sumber branch fitur baru. |
| `feature/<nama>` | Fitur baru | Dibuat dari `develop`, merge kembali ke `develop` lewat pull request. |
| `fix/<nama>` | Perbaikan bug non-darurat | Dibuat dari `develop`, merge kembali ke `develop`. |
| `hotfix/<nama>` | Perbaikan darurat produksi | Dibuat dari `main`, merge ke `main` **dan** `develop`. |
| `chore/<nama>` | Perapian, dependency, konfigurasi | Sama seperti `fix`. |

Branch berumur pendek. Setelah merge, hapus branch-nya.

## Alur Kerja Harian

1. Pastikan `develop` terbaru:

```bash
git switch develop
git pull origin develop
```

2. Buat branch fitur:

```bash
git switch -c feature/nama-fitur
```

3. Kerjakan perubahan, lalu commit dalam unit kecil.
4. Jalankan pemeriksaan sebelum push:

```bash
vendor/bin/pint --test
php artisan test
```

5. Push dan buka pull request ke `develop`:

```bash
git push -u origin feature/nama-fitur
```

6. Setelah review dan CI hijau, merge ke `develop` (squash atau merge commit).
7. Hapus branch fitur.

## Alur Rilis

1. Pastikan `develop` stabil dan semua test lulus.
2. Merge `develop` ke `main`:

```bash
git switch main
git pull origin main
git merge --no-ff develop
```

3. Beri tag versi:

```bash
git tag -a v1.2.0 -m "Rilis v1.2.0"
git push origin main --tags
```

4. Deploy dari `main`.

## Konvensi Commit

Gunakan format Conventional Commits:

```
<tipe>(<scope>): <ringkasan>
```

Tipe yang dipakai:

- `feat` — fitur baru
- `fix` — perbaikan bug
- `docs` — dokumentasi
- `test` — test
- `refactor` — perubahan struktur tanpa mengubah perilaku
- `chore` — dependency, konfigurasi, tooling
- `style` — format kode

Contoh:

```
feat(payments): tambah generator tagihan bulanan
fix(contact): validasi email pada form kontak
docs(roadmap): perbarui status fase 0
```

Hindari commit `--wip--`, `update`, atau `fix bug` tanpa keterangan.

## Checklist Pull Request

- [ ] Perubahan sesuai satu tujuan
- [ ] Migration dan seeder diperbarui bila perlu
- [ ] Test baru/updated menyertai perubahan
- [ ] `vendor/bin/pint --test` lulus
- [ ] `php artisan test` lulus
- [ ] Dokumentasi diperbarui bila mengubah perilaku
- [ ] Tidak ada kredensial atau file `.env` yang ikut ter-commit

## Pengaturan Repository (GitHub)

Aktifkan branch protection untuk `main` dan `develop`:

- Wajib pull request sebelum merge.
- Wajib status check CI lulus.
- Larang force push.
- Wajib review minimal 1 orang (jika tim bertambah).

## Struktur Branch Saat Ini

- `main` — riwayat lama, sinkron dengan `origin/main`.
- `develop` — integrasi fitur terbaru.
- Branch lama `student` sudah dihapus karena seluruh commit-nya sudah ada di `main`.
