<?php

namespace App\Models;

use Database\Factories\ContactMessageFactory;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'status',
        'read_at',
        'replied_at',
        'read_by',
        'replied_by',
    ];

    protected static function booted(): void
    {
        static::created(function (ContactMessage $message): void {
            Notification::make()
                ->title('Pesan Kontak Baru')
                ->body("{$message->name} mengirim pesan melalui formulir kontak.")
                ->icon('heroicon-o-envelope')
                ->sendToDatabase(User::whereIn('role', ['admin', 'operator'])->get());
        });
    }

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'replied_at' => 'datetime',
        ];
    }

    public function reader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function replier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function markAsRead(): void
    {
        if ($this->status === 'unread') {
            $this->forceFill([
                'status' => 'read',
                'read_at' => now(),
                'read_by' => auth()->id(),
            ])->save();
        }
    }

    public function markAsReplied(): void
    {
        if ($this->status !== 'replied') {
            $this->forceFill([
                'status' => 'replied',
                'replied_at' => now(),
                'replied_by' => auth()->id(),
            ])->save();
        }
    }

    public function isUnread(): bool
    {
        return $this->status === 'unread';
    }
}
