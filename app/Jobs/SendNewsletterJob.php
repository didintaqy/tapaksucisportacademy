<?php

namespace App\Jobs;

use App\Mail\NewsletterMail;
use App\Models\Newsletter;
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
            ->unique()
            ->values();

        foreach ($emails as $email) {
            Mail::to($email)->queue(new NewsletterMail($newsletter));
        }

        $newsletter->update([
            'status' => 'sent',
            'sent_at' => now(),
            'recipients_count' => $emails->count(),
        ]);

        Notification::make()
            ->title('Newsletter terkirim')
            ->body($newsletter->subject.' — '.$emails->count().' penerima.')
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
