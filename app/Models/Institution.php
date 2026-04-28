<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InstitutionType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
    ];

    protected $casts = [
        'type' => InstitutionType::class,
    ];

    public function academicRecords(): HasMany
    {
        return $this->hasMany(StudentAcademic::class);
    }

    public function billingEvents(): HasMany
    {
        return $this->hasMany(BillingEvent::class);
    }
}