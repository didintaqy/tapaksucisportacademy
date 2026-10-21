<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authorId = User::query()->value('id');

        $posts = [
            [
                'title' => 'Disiplin Lebih Penting Daripada Kekuatan dalam Tapak Suci',
                'category' => 'Tips & Trik',
                'tags' => ['tapak suci', 'filosofi', 'latihan'],
                'cover_image' => 'images/post-1.jpg',
                'excerpt' => 'Kekuatan fisik memang penting, tetapi disiplin adalah fondasi sejati seorang pesilat.',
                'content' => '<p>Dalam dunia pencak silat Tapak Suci, banyak pemula mengira bahwa kekuatan dan kecepatan adalah segalanya. Namun mereka yang bertahan lama justru menemukan bahwa disiplin adalah kunci utama.</p><h2>Mengapa Disiplin Itu Penting</h2><p>Disiplin membentuk karakter, konsistensi latihan, dan ketenangan dalam mengambil keputusan saat bertanding.</p><blockquote><p>Kekuatan bisa dilatih, tetapi disiplin adalah pilihan yang harus diambil setiap hari.</p></blockquote><ul><li>Latihan rutin membangun kebiasaan positif.</li><li>Kedisiplinan menurunkan risiko cedera.</li><li>Fokus yang terlatih meningkatkan prestasi.</li></ul>',
            ],
            [
                'title' => 'Teknik Dasar Pertahanan Diri yang Wajib Dikuasai',
                'category' => 'Latihan',
                'tags' => ['teknik', 'pemula', 'pencak silat'],
                'cover_image' => 'images/post-2.jpg',
                'excerpt' => 'Beberapa teknik sederhana yang bisa menyelamatkan diri Anda dalam situasi tak terduga.',
                'content' => '<p>Mengetahui teknik dasar pertahanan diri tidak memerlukan latihan bertahun-tahun. Yang dibutuhkan adalah pemahaman dan latihan yang konsisten.</p><h2>Langkah Awal Pertahanan Diri</h2><p>Mulailah dari kesadaran situasi, menjaga jarak, dan sikap waspada.</p><ul><li>Jaga keseimbangan tubuh.</li><li>Lindungi area vital.</li><li>Latih refleks menghindar.</li></ul>',
            ],
            [
                'title' => 'Membentuk Fokus dan Rasa Hormat Anak Melalui Latihan',
                'category' => 'Latihan',
                'tags' => ['pemula', 'kesehatan', 'latihan'],
                'cover_image' => 'images/post-3.jpg',
                'excerpt' => 'Latihan bela diri membantu anak tumbuh lebih fokus, percaya diri, dan menghormati orang lain.',
                'content' => '<p>Latihan bela diri bukan sekadar aktivitas fisik. Bagi anak-anak, ini adalah sarana membangun fokus, disiplin, dan rasa hormat.</p><h2>Manfaat untuk Anak</h2><p>Melalui latihan rutin, anak belajar menetapkan tujuan dan menghargai proses.</p><blockquote><p>Seorang anak yang belajar menghormati gurunya akan tumbuh menjadi pribadi yang menghormati kehidupan.</p></blockquote>',
            ],
            [
                'title' => 'Persiapan Menghadapi Kejuaraan Antar Dojo',
                'category' => 'Kejuaraan',
                'tags' => ['kompetisi', 'lanjutan', 'teknik'],
                'cover_image' => 'images/post-4.jpg',
                'excerpt' => 'Strategi dan persiapan fisik menjelang kejuaraan agar tampil maksimal.',
                'content' => '<p>Menghadapi kejuaraan membutuhkan persiapan matang, baik fisik maupun mental.</p><h2>Persiapan Menyeluruh</h2><ul><li>Latihan fisik terprogram.</li><li>Simulasi pertandingan.</li><li>Manajemen pola makan dan istirahat.</li></ul><p>Jangan lupakan aspek mental: visualisasi dan pengendalian emosi.</p>',
            ],
            [
                'title' => 'Membangun Kepercayaan Diri Sejak Dini',
                'category' => 'Tips & Trik',
                'tags' => ['pemula', 'filosofi'],
                'cover_image' => 'images/post-5.jpg',
                'excerpt' => 'Kepercayaan diri tumbuh dari pencapaian kecil yang konsisten.',
                'content' => '<p>Kepercayaan diri tidak muncul begitu saja. Ia dibangun melalui pencapaian-pencapaian kecil yang dilakukan secara konsisten.</p><h2>Langkah Sederhana</h2><p>Mulai dari target kecil, rayakan kemajuan, dan jangan takut gagal.</p><ul><li>Tetapkan target realistis.</li><li>Rayakan setiap kemajuan.</li><li>Belajar dari setiap kekalahan.</li></ul>',
            ],
            [
                'title' => 'Pentingnya Pemanasan Sebelum Latihan',
                'category' => 'Tips & Trik',
                'tags' => ['kesehatan', 'latihan', 'pemula'],
                'cover_image' => 'images/post-6.jpg',
                'excerpt' => 'Pemanasan yang benar mencegah cedera dan meningkatkan performa.',
                'content' => '<p>Pemanasan sering diabaikan padahal perannya sangat besar dalam mencegah cedera dan meningkatkan performa latihan.</p><h2>Manfaat Pemanasan</h2><ul><li>Melancarkan aliran darah ke otot.</li><li>Meningkatkan kelenturan.</li><li>Menyiapkan tubuh secara mental.</li></ul>',
            ],
        ];

        foreach ($posts as $index => $post) {
            $categoryId = Category::where('name', $post['category'])->value('id');
            $tagIds = Tag::whereIn('name', $post['tags'])->pluck('id');

            $model = Post::updateOrCreate(
                ['slug' => str($post['title'])->slug()->value()],
                [
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'content' => $post['content'],
                    'cover_image' => $post['cover_image'],
                    'category_id' => $categoryId,
                    'author_id' => $authorId,
                    'status' => PostStatus::Published,
                    'published_at' => now()->subDays(($index + 1) * 5),
                    'is_featured' => $index === 0,
                    'views' => ($index + 1) * 37,
                ],
            );

            $model->tags()->sync($tagIds);
        }

        $this->command->info('Artikel blog siap ('.count($posts).' tulisan).');
    }
}
