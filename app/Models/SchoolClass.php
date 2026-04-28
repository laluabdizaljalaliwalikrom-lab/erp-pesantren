<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = ['institution_id', 'name'];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}