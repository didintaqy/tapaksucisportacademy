<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Traits\LogsActivity;
use Database\Factories\PaymentFactory;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory, SoftDeletes;

    use LogsActivity;

    /**
     * Alasan & metadata sementara untuk audit trail status.
     * Diisi sebelum menyimpan perubahan status, lalu dibersihkan.
     */
    public ?string $statusChangeReason = null;

    /** @var array<string, mixed> */
    public array $statusChangeMetadata = [];

    protected $fillable = [
        'student_id',
        'payment_type_id',
        'invoice_number',
        'period',
        'description',
        'amount',
        'status',
        'payment_method',
        'due_date',
        'paid_at',
        'reminded_at',
        'reminder_count',
        'proof_file',
        'gateway_name',
        'gateway_reference',
        'gateway_payload',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PaymentStatus::class,
            'payment_method' => PaymentMethod::class,
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'reminded_at' => 'datetime',
            'reminder_count' => 'integer',
            'gateway_payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Payment $payment): void {
            if (blank($payment->invoice_number)) {
                $payment->invoice_number = static::generateInvoiceNumber();
            }
        });

        static::saving(function (Payment $payment): void {
            if ($payment->status === PaymentStatus::Paid && blank($payment->paid_at)) {
                $payment->paid_at = now();
            }
        });

        static::updated(function (Payment $payment): void {
            if (! $payment->wasChanged('status')) {
                $payment->statusChangeReason = null;
                $payment->statusChangeMetadata = [];

                return;
            }

            $from = $payment->getOriginal('status');
            $to = $payment->status;

            PaymentStatusHistory::create([
                'payment_id' => $payment->getKey(),
                'from_status' => $from instanceof PaymentStatus ? $from->value : $from,
                'to_status' => $to instanceof PaymentStatus ? $to->value : $to,
                'changed_by' => auth()->id(),
                'reason' => $payment->statusChangeReason ?? 'Perubahan status manual',
                'metadata' => $payment->statusChangeMetadata ?: null,
            ]);

            if ($to === PaymentStatus::Paid) {
                Notification::make()
                    ->title('Pembayaran Diterima')
                    ->body("Pembayaran {$payment->invoice_number} dari {$payment->student?->full_name} telah lunas.")
                    ->icon('heroicon-o-banknotes')
                    ->success()
                    ->sendToDatabase(User::whereIn('role', ['admin', 'operator'])->get());
            }

            $payment->statusChangeReason = null;
            $payment->statusChangeMetadata = [];
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(PaymentStatusHistory::class)->latest('created_at');
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', PaymentStatus::Paid->value);
    }

    public function scopeOutstanding(Builder $query): Builder
    {
        return $query->whereIn('status', array_map(
            fn (PaymentStatus $status) => $status->value,
            PaymentStatus::outstanding()
        ));
    }

    public function scopeForPeriod(Builder $query, string $period): Builder
    {
        return $query->where('period', $period);
    }

    /**
     * Tagihan yang perlu dikirimi pengingat: belum lunas, jatuh tempo
     * dalam N hari ke depan (termasuk yang sudah lewat), dan belum
     * diingatkan hari ini.
     */
    public function scopeDueForReminder(Builder $query, int $daysBefore = 3): Builder
    {
        return $query
            ->outstanding()
            ->whereDate('due_date', '<=', now()->addDays($daysBefore)->toDateString())
            ->where(function (Builder $inner): void {
                $inner->whereNull('reminded_at')
                    ->orWhereDate('reminded_at', '<', now()->toDateString());
            });
    }

    public function scopeWithGateway(Builder $query): Builder
    {
        return $query->where(function (Builder $inner): void {
            $inner->whereNotNull('gateway_reference')
                ->orWhereNotNull('gateway_name');
        });
    }

    /**
     * Status transaksi terakhir yang dilaporkan gateway (mis. Midtrans).
     */
    public function gatewayTransactionStatus(): ?string
    {
        $value = data_get($this->gateway_payload, 'last_callback.transaction_status');

        return filled($value) ? (string) $value : null;
    }

    /**
     * Status lokal yang seharusnya berdasarkan payload gateway terakhir.
     */
    public function expectedStatusFromGateway(): ?PaymentStatus
    {
        return match ($this->gatewayTransactionStatus()) {
            'settlement', 'capture' => PaymentStatus::Paid,
            'pending' => PaymentStatus::Pending,
            'deny' => PaymentStatus::Failed,
            'cancel', 'refund', 'partial_refund' => PaymentStatus::Cancelled,
            'expire' => PaymentStatus::Expired,
            default => null,
        };
    }

    public function hasGatewayMismatch(): bool
    {
        $expected = $this->expectedStatusFromGateway();

        return $expected !== null && $this->status !== $expected;
    }

    public function gatewayLastCallbackAt(): ?Carbon
    {
        $value = data_get($this->gateway_payload, 'last_callback.transaction_time')
            ?? data_get($this->gateway_payload, 'last_callback.settlement_time');

        return filled($value) ? Carbon::parse($value) : null;
    }

    public function markAsReminded(): void
    {
        $this->forceFill([
            'reminded_at' => now(),
            'reminder_count' => ($this->reminder_count ?? 0) + 1,
        ])->save();
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }

    public function isOutstanding(): bool
    {
        return in_array($this->status, PaymentStatus::outstanding(), true);
    }

    public function markAsPaid(?PaymentMethod $method = null, ?Carbon $paidAt = null, ?string $reason = null): void
    {
        $this->statusChangeReason = $reason ?? 'Pembayaran ditandai lunas';

        $this->forceFill([
            'status' => PaymentStatus::Paid,
            'payment_method' => $method ?? $this->payment_method,
            'paid_at' => $paidAt ?? now(),
        ])->save();
    }

    public function markAsPending(?PaymentMethod $method = null, ?string $reason = null): void
    {
        $this->statusChangeReason = $reason ?? 'Pembayaran menunggu konfirmasi';

        $this->forceFill([
            'status' => PaymentStatus::Pending,
            'payment_method' => $method ?? $this->payment_method,
        ])->save();
    }

    /**
     * Ubah status pembayaran sambil mencatat audit trail.
     *
     * @param  array<string, mixed>  $metadata
     */
    public function changeStatus(PaymentStatus $status, ?string $reason = null, array $metadata = []): void
    {
        $this->statusChangeReason = $reason;
        $this->statusChangeMetadata = $metadata;

        $this->forceFill(['status' => $status])->save();

        $this->statusChangeReason = null;
        $this->statusChangeMetadata = [];
    }

    public function formattedAmount(): string
    {
        return 'Rp '.number_format((float) $this->amount, 0, ',', '.');
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-'.now()->format('Ymd').'-';

        do {
            $number = $prefix.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::withTrashed()->where('invoice_number', $number)->exists());

        return $number;
    }
}
