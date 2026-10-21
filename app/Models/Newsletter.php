<?php

namespace App\Models;

use App\Jobs\SendNewsletterJob;
use Database\Factories\NewsletterFactory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function isQueued(): bool
    {
        return $this->status === 'queued';
    }

    public function getIsDraftAttribute(): bool
    {
        return $this->isDraft();
    }

    public function setIsDraftAttribute(bool $value): void
    {
        if ($this->isQueued()) {
            return;
        }

        $this->status = $value ? 'draft' : 'sent';
        if (! $value && is_null($this->sent_at)) {
            $this->sent_at = now();
        }
    }

    public function send(?User $actor = null): void
    {
        if ($actor && ! $actor->can('send', $this)) {
            throw new AuthorizationException('Anda tidak berhak mengirim newsletter ini.');
        }

        if ($this->isSent() || $this->isQueued()) {
            return;
        }

        $this->update(['status' => 'queued']);

        SendNewsletterJob::dispatch($this);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }
}
