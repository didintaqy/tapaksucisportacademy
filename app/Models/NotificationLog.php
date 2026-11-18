<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationLog extends Model
{
    public const CHANNELS = ['email', 'whatsapp', 'sms'];

    public const STATUSES = ['queued', 'sent', 'failed'];

    protected $fillable = [
        'channel',
        'recipient',
        'template',
        'subject_type',
        'subject_id',
        'status',
        'provider_ref',
        'error',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function markAsSent(?string $providerRef = null): void
    {
        $this->forceFill([
            'status' => 'sent',
            'provider_ref' => $providerRef,
            'sent_at' => now(),
            'error' => null,
        ])->save();
    }

    public function markAsFailed(string $error): void
    {
        $this->forceFill([
            'status' => 'failed',
            'error' => $error,
        ])->save();
    }
}
