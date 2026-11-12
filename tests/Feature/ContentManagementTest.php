<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Jobs\SendNewsletterJob;
use App\Mail\NewsletterMail;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Newsletter;
use App\Models\NewsletterDelivery;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Post;
use App\Models\Student;
use App\Models\StudentRegistration;
use App\Models\Tag;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function category_slug_auto_generated(): void
    {
        $category = Category::create(['name' => 'Tips & Trik']);
        $this->assertSame('tips-trik', $category->slug);
    }

    #[Test]
    public function tag_slug_auto_generated(): void
    {
        $tag = Tag::create(['name' => 'Teknik Lanjutan']);
        $this->assertSame('teknik-lanjutan', $tag->slug);
    }

    #[Test]
    public function post_can_have_category_and_tags(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Latihan']);
        $tag1 = Tag::factory()->create(['name' => 'pemula']);
        $tag2 = Tag::factory()->create(['name' => 'teknik']);

        $post = Post::create([
            'title' => 'Artikel dengan Kategori',
            'slug' => 'artikel-dengan-kategori',
            'excerpt' => 'Ringkasan',
            'content' => '<p>Isi artikel</p>',
            'category_id' => $category->id,
            'status' => PostStatus::Published->value,
            'published_at' => now(),
            'author_id' => $admin->id,
        ]);

        $post->tags()->attach([$tag1->id, $tag2->id]);

        $this->assertSame($category->id, $post->category_id);
        $this->assertCount(2, $post->tags);
    }

    #[Test]
    public function admin_can_create_and_send_newsletter(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        User::factory()->create(['email' => 'user1@test.com']);
        User::factory()->create(['email' => 'user2@test.com']);

        $newsletter = Newsletter::create([
            'subject' => 'Test Newsletter',
            'content' => '<p>Isi newsletter</p>',
            'status' => 'draft',
            'created_by' => $admin->id,
        ]);

        $newsletter->send();

        $newsletter->refresh();
        $this->assertSame('sent', $newsletter->status);
        $this->assertNotNull($newsletter->sent_at);
        $this->assertSame(3, $newsletter->recipients_count);
    }

    #[Test]
    public function newsletter_send_dispatches_job_when_queue_is_not_synchronous(): void
    {
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $newsletter = Newsletter::create([
            'subject' => 'Newsletter Antrean',
            'content' => '<p>Isi newsletter</p>',
            'status' => 'draft',
            'created_by' => $admin->id,
        ]);

        $newsletter->send($admin);

        $this->assertSame('queued', $newsletter->fresh()->status);

        Queue::assertPushed(
            SendNewsletterJob::class,
            fn (SendNewsletterJob $job): bool => $job->newsletter->is($newsletter),
        );
    }

    #[Test]
    public function newsletter_job_queues_mail_for_each_recipient(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        User::factory()->create(['email' => 'user1@test.com']);
        User::factory()->create(['email' => 'user2@test.com']);
        Student::factory()->create(['email' => 'siswa@test.com']);

        $newsletter = Newsletter::create([
            'subject' => 'Newsletter Queue',
            'content' => '<p>Isi newsletter</p>',
            'status' => 'queued',
            'created_by' => $admin->id,
        ]);

        (new SendNewsletterJob($newsletter))->handle();

        $newsletter->refresh();
        $this->assertSame('sent', $newsletter->status);
        $this->assertSame(4, $newsletter->recipients_count);

        Mail::assertQueued(NewsletterMail::class, 4);
    }

    #[Test]
    public function newsletter_job_is_idempotent_per_recipient_on_retry(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create(['email' => 'admin@test.com']);
        User::factory()->create(['email' => 'user1@test.com']);
        Student::factory()->create(['email' => 'siswa1@test.com']);

        $newsletter = Newsletter::create([
            'subject' => 'Newsletter Idempoten',
            'content' => '<p>Isi newsletter</p>',
            'status' => 'queued',
            'created_by' => $admin->id,
        ]);

        // Simulasikan sebagian penerima sudah tercatat pada percobaan pertama sebelum gagal
        NewsletterDelivery::create([
            'newsletter_id' => $newsletter->id,
            'email' => 'user1@test.com',
            'sent_at' => now()->subMinute(),
        ]);

        (new SendNewsletterJob($newsletter))->handle();

        // Hanya 2 penerima sisanya (admin & siswa1) yang boleh dikirimi email baru
        Mail::assertQueued(NewsletterMail::class, 2);

        $newsletter->refresh();
        $this->assertSame('sent', $newsletter->status);
        $this->assertSame(3, $newsletter->recipients_count);
    }

    #[Test]
    public function failed_newsletter_job_returns_to_draft(): void
    {
        $admin = User::factory()->admin()->create();
        $newsletter = Newsletter::create([
            'subject' => 'Newsletter Gagal',
            'content' => '<p>Isi newsletter</p>',
            'status' => 'queued',
            'created_by' => $admin->id,
        ]);

        (new SendNewsletterJob($newsletter))->failed(new \RuntimeException('SMTP down'));

        $this->assertSame('draft', $newsletter->fresh()->status);
    }

    #[Test]
    public function admin_can_open_newsletter_list_with_queue_status(): void
    {
        $admin = User::factory()->admin()->create();
        Newsletter::factory()->create([
            'subject' => 'Newsletter Dalam Antrean',
            'status' => 'queued',
        ]);

        $this->actingAs($admin)
            ->get('/admin/newsletters')
            ->assertOk()
            ->assertSee('Newsletter Dalam Antrean')
            ->assertSee('Antrean');
    }

    #[Test]
    public function newsletter_draft_not_sent(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();

        $newsletter = Newsletter::create([
            'subject' => 'Draft Newsletter',
            'content' => '<p>Isi draft</p>',
            'status' => 'draft',
            'created_by' => $admin->id,
        ]);

        // Don't call send()
        $this->assertSame('draft', $newsletter->status);
        $this->assertNull($newsletter->sent_at);
        $this->assertSame(0, $newsletter->recipients_count);
        Mail::assertNothingSent();
    }

    #[Test]
    public function operator_cannot_publish_post_only_admin(): void
    {
        $operator = User::factory()->create(['role' => UserRole::Operator->value]);
        $admin = User::factory()->admin()->create();

        // Operator creates draft
        $post = Post::create([
            'title' => 'Artikel Operator',
            'slug' => 'artikel-operator',
            'excerpt' => 'Ringkasan',
            'content' => '<p>Isi</p>',
            'status' => PostStatus::Draft->value,
            'author_id' => $operator->id,
        ]);

        $this->assertSame(PostStatus::Draft, $post->status);

        // Operator submits for review
        $post->update(['status' => PostStatus::Review->value]);
        $this->assertSame(PostStatus::Review, $post->status);

        // Operator tries to publish - should be prevented by policy
        $this->assertFalse($operator->can('updateStatus', [$post, PostStatus::Published]));
        $this->assertTrue($admin->can('updateStatus', [$post, PostStatus::Published]));

        // Admin publishes
        $post->update(['status' => PostStatus::Published->value, 'published_at' => now()]);
        $this->assertSame(PostStatus::Published, $post->status);
    }

    #[Test]
    public function database_notifications_work(): void
    {
        $admin = User::factory()->admin()->create();

        Notification::make()
            ->title('Test Notifikasi')
            ->body('Isi notifikasi')
            ->success()
            ->sendToDatabase($admin);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $admin->id,
        ]);

        $notification = DatabaseNotification::first();
        $this->assertSame('Test Notifikasi', $notification->data['title']);
        $this->assertSame('Isi notifikasi', $notification->data['body']);
    }

    #[Test]
    public function post_submission_creates_notification_for_admins(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->create(['role' => UserRole::Operator->value]);

        // Operator creates draft post - triggers notification via model event
        Post::create([
            'title' => 'Artikel Baru',
            'slug' => 'artikel-baru',
            'excerpt' => 'Ringkasan',
            'content' => '<p>Isi</p>',
            'status' => PostStatus::Draft->value,
            'author_id' => $operator->id,
        ]);

        // Check notification created for admin
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $admin->id,
        ]);

        $notification = DatabaseNotification::latest()->first();
        $this->assertStringContainsString('Artikel Baru', $notification->data['title']);
    }

    #[Test]
    public function contact_message_creates_notification(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->create(['role' => UserRole::Operator->value]);

        ContactMessage::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '08123456789',
            'message' => 'Halo, saya ingin bertanya...',
            'status' => 'unread',
        ]);

        // Check notification for admin
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $admin->id,
        ]);

        $notification = DatabaseNotification::latest()->first();
        $this->assertStringContainsString('Pesan Kontak Baru', $notification->data['title']);
    }

    #[Test]
    public function student_registration_creates_notification(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->create(['role' => UserRole::Operator->value]);

        StudentRegistration::create([
            'full_name' => 'Ahmad Rizki',
            'nickname' => 'Rizki',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'education' => 'SD',
            'parent_name' => 'Budi',
            'phone' => '08123456789',
            'email' => 'rizki@example.com',
            'address' => 'Jl. Test',
            'branch' => 'Tapak Suci',
            'notes' => '',
            'status' => 'new',
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $admin->id,
        ]);

        $notification = DatabaseNotification::latest()->first();
        $this->assertStringContainsString('Pendaftaran Siswa Baru', $notification->data['title']);
    }

    #[Test]
    public function payment_paid_creates_notification(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->create(['role' => UserRole::Operator->value]);
        $student = Student::factory()->create(['full_name' => 'Siti Rahma']);

        $payment = Payment::create([
            'student_id' => $student->id,
            'payment_type_id' => PaymentType::factory()->create()->id,
            'invoice_number' => 'INV-TEST-001',
            'amount' => 100000,
            'status' => PaymentStatus::Pending,
            'due_date' => now()->addDays(7),
        ]);

        $payment->update(['status' => PaymentStatus::Paid]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $admin->id,
        ]);

        $notification = DatabaseNotification::latest()->first();
        $this->assertStringContainsString('Pembayaran Diterima', $notification->data['title']);
        $this->assertStringContainsString('INV-TEST-001', $notification->data['body']);
    }
}
