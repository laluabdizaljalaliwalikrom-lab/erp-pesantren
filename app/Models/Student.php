<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasUuids;

    protected $fillable = [
        'nis',
        'full_name',
        'residency_status',
        'special_condition',
    ];

    public function academicRecords(): HasMany
    {
        return $this->hasMany(StudentAcademic::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}