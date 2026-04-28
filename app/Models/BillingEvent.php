<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingEvent extends Model
{
    use HasUuids;

    protected $fillable = [
        'institution_id',
        'name',
        'amount',
        'target_residency',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}