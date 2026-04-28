<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AcademicYear;

class AcademicYearObserver
{
    public function saving(AcademicYear $academicYear): void
    {
        if ($academicYear->isDirty('is_active') && $academicYear->is_active) {
            // Deactivate all other academic years
            AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        }
    }
}