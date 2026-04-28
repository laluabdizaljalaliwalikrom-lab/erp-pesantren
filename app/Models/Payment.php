<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\LogsProfessionalActivity;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasUuids, SoftDeletes, LogsProfessionalActivity;

    protected $fillable = [
        'bill_id',
        'external_id',
        'snap_token',
        'amount',
        'amount_received',
        'method',
        'transaction_id',
        'reference_id',
        'status',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => PaymentStatus::class,
    ];

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaymentItem::class);
    }

    protected static function booted(): void
    {
        // Auto-assign status based on payment method before creation.
        static::creating(static function (self $payment): void {
            if ($payment->status === null) {
                $payment->status = $payment->method === 'cash'
                    ? PaymentStatus::CONFIRMED
                    : PaymentStatus::PENDING;
            }
        });

        // Sync bill statuses whenever a payment is saved or deleted.
        static::saved(static function (self $payment): void {
            // Immediate cleanup for REJECTED payments
            if ($payment->status === PaymentStatus::REJECTED) {
                // Manually delete items first to be safe, then force delete the payment
                $payment->items()->delete();
                $payment->forceDelete();
                return;
            }

            // For single bill payments
            $payment->bill?->refreshStatus();
            
            // For bulk payments
            foreach ($payment->items as $item) {
                $item->bill?->refreshStatus();
            }
        });

        static::deleted(static function (self $payment): void {
            $payment->bill?->refreshStatus();
            foreach ($payment->items as $item) {
                $item->bill?->refreshStatus();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            Student::class,
            Bill::class,
            'id', // Foreign key on bills table
            'id', // Foreign key on students table
            'bill_id', // Local key on payments table
            'student_id' // Local key on bills table
        );
    }
}