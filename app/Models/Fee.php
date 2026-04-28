<?php

namespace App\Models;

use App\Enums\FeeFrequency;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fee extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'amount',
        'frequency',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'frequency' => FeeFrequency::class,
    ];
}
