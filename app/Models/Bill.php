<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\LogsProfessionalActivity;
use App\Enums\BillStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use HasUuids, SoftDeletes, LogsProfessionalActivity;

    protected $fillable = [
        'student_id',
        'fee_id',
        'academic_year_id',
        'amount',
        'original_amount',
        'discount_amount',
        'final_amount',
        'due_date',
        'period_name',
        'status',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount'    => 'decimal:2',
        'due_date'        => 'date',
        'status'         => \App\Enums\BillStatus::class,
    ];

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()
            ->where('status', PaymentStatus::CONFIRMED)
            ->sum('amount');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return (float) ($this->final_amount - $this->total_paid);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function fee(): BelongsTo
    {
        return $this->belongsTo(Fee::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Recalculate and persist the bill status based on payment totals.
     */
    public function refreshStatus(): void
    {
        $directTotal = (float) $this->payments()
            ->where('status', PaymentStatus::CONFIRMED)
            ->sum('amount');

        $bulkTotal = (float) \App\Models\PaymentItem::query()
            ->where('bill_id', $this->id)
            ->whereHas('payment', fn($q) => $q->where('status', PaymentStatus::CONFIRMED))
            ->sum('amount');

        $totalPaid = $directTotal + $bulkTotal;

        $finalAmount = (float) ($this->final_amount ?? 0);

        $this->status = match (true) {
            $totalPaid >= $finalAmount && $finalAmount > 0 => BillStatus::PAID,
            $totalPaid > 0                                 => BillStatus::PARTIAL,
            default                                        => BillStatus::UNPAID,
        };

        $this->saveQuietly();
    }
}