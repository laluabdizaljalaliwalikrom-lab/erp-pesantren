<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Announcement extends Model
{
    use HasUuids;

    protected $fillable = [
        'title',
        'content',
        'category',
        'is_active',
        'published_at',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];
}
