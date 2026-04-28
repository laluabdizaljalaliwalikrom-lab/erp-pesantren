<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AcademicStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAcademic extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'student_academic';

    protected $fillable = [
        'student_id',
        'institution_id',
        'academic_year_id',
        'school_class_id',
        'semester',
        'status',
    ];

    protected $casts = [
        'active' => 'boolean',
        'status' => AcademicStatus::class,
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }
}