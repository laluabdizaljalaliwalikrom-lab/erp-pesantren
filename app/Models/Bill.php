<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    use HasUuids;

    protected $fillable = [
        'student_id',
        'billing_event_id',
        'original_amount',
        'discount_amount',
        'final_amount',
        'paid_amount',
        'status',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount'    => 'decimal:2',
        'paid_amount'     => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function billingEvent(): BelongsTo
    {
        return $this->belongsTo(BillingEvent::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}