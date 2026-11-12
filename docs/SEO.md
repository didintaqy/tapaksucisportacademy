# SEO

Website ini dioptimalkan untuk SEO dengan:

- **Meta Title & Description** — diset di setiap halaman frontend (via View Composer).
- **Sitemap** — `/sitemap.xml` menampilkan semua halaman penting dan di-ping ke Google.
- **Open Graph** - (disiapkan callback); template dasar sudah menyertakan meta opengraph di head.

## Meta Tags per Halaman

Aktivitas metatags didukung oleh View Composer di `AppServiceProvider.php`:

| Halaman | Respons |
|---|---|
| Homepage | `@seo` component dengan judul, description (default). |
| About | "Tentang Kami" — deskripsi. |
| Program | "Program Latihan" — daftar program. |
| Gallery | "Galeri Kegiatan" — foto. |
| Blog | "Artikel & Berita" — list. |
| Contact | "Hubungi Kami" — quick info. |
| Testimoni | "Testimoni" — testimoni peserta. |
| FAQ | "FAQ" — pertanyaan umum. |
| Jadwal | "Jadwal Latihan" — jadwal per hari. |
| Pendaftaran | "Pendaftaran Siswa" — form. |

Arahkan web server (Nginx/Apache) untuk me-serve `sitemap.xml` dari folder `public/`.

## Sitemap

1. Jadikan SitemapController terdaftar sebagai route `/sitemap.xml`.
2. Setiap route yang tidak mengandung placeholder ID (mis. `/blog/{slug}`) dikirimkan ke sitemap.
3. `changefreq` default: daily, `priority` default: 0.8.
4. Untuk memperbarui sitemap secara otomatis, gunakan tool cron `/cron/sitemap- ping google`.