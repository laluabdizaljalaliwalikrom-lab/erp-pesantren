<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\StudentAcademic;
use Illuminate\Support\Facades\DB;

class StudentAcademicService
{
    /**
     * Bulk assign students to an academic class.
     *
     * @param array<string, mixed> $data
     * @return int Number of successfully created assignments
     */
    public function bulkAssign(array $data): int
    {
        return DB::transaction(function () use ($data): int {
            $assignedCount = 0;

            foreach ($data['student_ids'] as $studentId) {
                StudentAcademic::updateOrCreate(
                    [
                        'student_id'       => $studentId,
                        'institution_id'   => $data['institution_id'],
                        'academic_year_id' => $data['academic_year_id'],
                        'semester'         => $data['semester'],
                    ],
                    [
                        'school_class_id'  => $data['school_class_id'],
                        'status'           => \App\Enums\AcademicStatus::ACTIVE,
                    ]
                );
                
                $assignedCount++;
            }

            return $assignedCount;
        });
    }
}