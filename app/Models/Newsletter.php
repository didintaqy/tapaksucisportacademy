<?php

namespace App\Models;

use Database\Factories\NewsletterFactory;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;

class Newsletter extends Model
{
    /** @use HasFactory<NewsletterFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject',
        'content',
        'status',
        'sent_at',
        'recipients_count',
        'created_by',
    ];

    protected $attributes = [
        'recipients_count' => 0,
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'recipients_count' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function getIsDraftAttribute(): bool
    {
        return $this->isDraft();
    }

    public function setIsDraftAttribute(bool $value): void
    {
        $this->status = $value ? 'draft' : 'sent';
        if (! $value && is_null($this->sent_at)) {
            $this->sent_at = now();
        }
    }

    public function send(): void
    {
        if ($this->isSent()) {
            return;
        }

        $emails = User::whereNotNull('email')->where('email', '!=', '')->pluck('email')
            ->merge(Student::whereNotNull('email')->where('email', '!=', '')->pluck('email'))
            ->unique()
            ->values();

        $sent = 0;

        // ponytail: kirim sinkron per penerima; pindah ke queued Mailable kalau penerima > ~500.
        foreach ($emails as $email) {
            try {
                Mail::html($this->content, function ($message) use ($email): void {
                    $message->to($email)->subject($this->subject);
                });
                $sent++;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'recipients_count' => $sent,
        ]);

        Notification::make()
            ->title('Newsletter terkirim')
            ->body("{$this->subject} — {$sent} penerima.")
            ->success()
            ->sendToDatabase(User::where('role', 'admin')->get());
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }
}
