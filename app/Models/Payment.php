<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory, SoftDeletes;

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

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }

    public function markAsPaid(?PaymentMethod $method = null, ?Carbon $paidAt = null): void
    {
        $this->forceFill([
            'status' => PaymentStatus::Paid,
            'payment_method' => $method ?? $this->payment_method,
            'paid_at' => $paidAt ?? now(),
        ])->save();
    }

    public function markAsPending(?PaymentMethod $method = null): void
    {
        $this->forceFill([
            'status' => PaymentStatus::Pending,
            'payment_method' => $method ?? $this->payment_method,
        ])->save();
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
