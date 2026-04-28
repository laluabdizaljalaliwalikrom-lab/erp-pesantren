<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasUuids;

    protected $fillable = [
        'type',
        'message',
        'status',
        'retry_count',
    ];

    protected $casts = [
        'retry_count' => 'integer',
    ];
}