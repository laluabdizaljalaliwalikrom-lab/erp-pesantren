<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\LogsProfessionalActivity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Student extends Model
{
    use HasUuids, SoftDeletes, LogsProfessionalActivity;

    protected $fillable = [
        'photo',
        'nis',
        'nisn',
        'nik',
        'no_kk',
        'full_name',
        'gender',
        'residency_status',
        'special_condition',
        'guardian_id',
        'birth_place',
        'birth_date',
        'address',
        'rt',
        'rw',
        'dusun',
        'village',
        'district',
        'father_name',
        'mother_name',
        'father_nik',
        'mother_nik',
        'hp',
        'email',
        'previous_school',
        'sibling_position',
        'sibling_count',
        'guardian_phone',
        'status',
        'graduation_year',
        'after_graduation_status',
    ];

    protected $casts = [
        'residency_status'  => \App\Enums\ResidencyStatus::class,
        'special_condition' => \App\Enums\StudentCondition::class,
        'status'            => \App\Enums\StudentStatus::class,
        'gender'            => \App\Enums\Gender::class,
        'birth_date'        => 'date',
    ];

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }

    public function academicRecords(): HasMany
    {
        return $this->hasMany(StudentAcademic::class);
    }

    public function academic(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StudentAcademic::class)
            ->where('status', \App\Enums\AcademicStatus::ACTIVE)
            ->latestOfMany();
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function institution(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            Institution::class,
            StudentAcademic::class,
            'student_id',
            'id',
            'id',
            'institution_id'
        )->where('student_academic.status', \App\Enums\AcademicStatus::ACTIVE);
    }
}