<?php

namespace App\Jobs;

use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\NewsletterDelivery;
use App\Models\Student;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(public Newsletter $newsletter) {}

    public function handle(): void
    {
        $newsletter = $this->newsletter->fresh();

        if (! $newsletter || $newsletter->isSent()) {
            return;
        }

        $emails = User::whereNotNull('email')->where('email', '!=', '')->pluck('email')
            ->merge(Student::whereNotNull('email')->where('email', '!=', '')->pluck('email'))
            ->map(fn ($email) => strtolower(trim((string) $email)))
            ->filter()
            ->unique()
            ->values();

        $alreadyDelivered = NewsletterDelivery::query()
            ->where('newsletter_id', $newsletter->id)
            ->pluck('email')
            ->map(fn ($email) => strtolower(trim((string) $email)))
            ->all();

        $alreadyDeliveredLookup = array_flip($alreadyDelivered);

        foreach ($emails as $email) {
            if (isset($alreadyDeliveredLookup[$email])) {
                continue;
            }

            $delivery = NewsletterDelivery::firstOrCreate(
                [
                    'newsletter_id' => $newsletter->id,
                    'email' => $email,
                ],
                [
                    'sent_at' => now(),
                ],
            );

            if ($delivery->wasRecentlyCreated) {
                Mail::to($email)->queue(new NewsletterMail($newsletter));
            }
        }

        $totalDelivered = NewsletterDelivery::query()
            ->where('newsletter_id', $newsletter->id)
            ->count();

        $newsletter->update([
            'status' => 'sent',
            'sent_at' => now(),
            'recipients_count' => $totalDelivered,
        ]);

        Notification::make()
            ->title('Newsletter terkirim')
            ->body($newsletter->subject.' — '.$totalDelivered.' penerima.')
            ->success()
            ->sendToDatabase(User::where('role', 'admin')->get());
    }

    public function failed(?Throwable $exception): void
    {
        $newsletter = $this->newsletter->fresh();

        if (! $newsletter) {
            return;
        }

        $newsletter->update(['status' => 'draft']);

        Notification::make()
            ->title('Newsletter gagal dikirim')
            ->body("{$newsletter->subject} gagal diproses. Silakan coba kirim ulang.")
            ->danger()
            ->sendToDatabase(User::where('role', 'admin')->get());
    }
}
