<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Student;
use App\Models\Bill;
use App\Models\Fee;
use App\Models\StudentAcademic;
use App\Models\AcademicYear;
use App\Enums\FeeFrequency;
use App\Enums\StudentCondition;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillingService
{
    /**
     * Generate Mass Bills based on the given filters and fee configuration.
     *
     * @param array<string, mixed> $data
     * @return int Number of successfully generated bills
     */
    public function generateMassBills(array $data): int
    {
        return DB::transaction(function () use ($data): int {
            $generatedCount = 0;
            
            // 1. Fetch the Fee
            $fee = Fee::findOrFail($data['fee_id']);
            
            // 2. Fetch the Academic Year for base dates
            $academicYear = AcademicYear::findOrFail($data['academic_year_id']);
            $startDate = Carbon::parse($academicYear->start_date);
            
            // 3. Build Query for Active Students
            $query = StudentAcademic::where('status', \App\Enums\AcademicStatus::ACTIVE)
                                   ->where('academic_year_id', $data['academic_year_id']);
            
            if (!empty($data['institution_id'])) {
                $query->where('institution_id', $data['institution_id']);
            }
            if (!empty($data['school_class_id'])) {
                $query->where('school_class_id', $data['school_class_id']);
            }
            if (!empty($data['student_id'])) {
                $query->where('student_id', $data['student_id']);
            }
            if (!empty($data['residency_status'])) {
                $query->whereHas('student', function ($q) use ($data): void {
                    $q->where('residency_status', $data['residency_status']);
                });
            }
            
            // Get unique students that match the criteria to avoid multiple identical bills for double-enrolled students
            $activeStudents = $query->get()->unique('student_id');

            // 4. Generate Bills per student
            foreach ($activeStudents as $record) {
                switch ($fee->frequency) {
                    case FeeFrequency::MONTHLY:
                        for ($i = 0; $i < 12; $i++) {
                            $dueDate = $startDate->copy()->addMonths($i);
                            $periodName = $dueDate->translatedFormat('F Y'); // e.g., "Juli 2025"
                            
                            $this->createBill($record->student_id, $fee, $academicYear->id, $periodName, $dueDate);
                            $generatedCount++;
                        }
                        break;
                        
                    case FeeFrequency::SEMESTER:
                        $semesters = empty($data['semester']) ? [1, 2] : [$data['semester']];
                        
                        foreach ($semesters as $sem) {
                            $periodName = "Semester {$sem}";
                            // Start of academic year for sem 1, start + 6 months for sem 2
                            $dueDate = $sem === 1 ? $startDate->copy() : $startDate->copy()->addMonths(6);
                            
                            $this->createBill($record->student_id, $fee, $academicYear->id, $periodName, $dueDate);
                            $generatedCount++;
                        }
                        break;
                        
                    case FeeFrequency::YEARLY:
                    case FeeFrequency::INCIDENTAL:
                    default:
                        $periodName = $fee->frequency === FeeFrequency::YEARLY ? 'Tahunan' : 'Insidental';
                        $dueDate = $startDate->copy();
                        
                        $this->createBill($record->student_id, $fee, $academicYear->id, $periodName, $dueDate);
                        $generatedCount++;
                        break;
                }
            }

            return $generatedCount;
        });
    }

    /**
     * Create or update a bill securely ensuring no duplicates.
     */
    private function createBill(
        string $studentId, 
        Fee $fee, 
        string $academicYearId, 
        string $periodName, 
        Carbon $dueDate
    ): void {
        $student = Student::findOrFail($studentId);
        $condition = $student->special_condition ?? StudentCondition::NONE;
        
        $originalAmount = (float) $fee->amount;
        $discountRate = $condition->getDiscountRate();
        $discountAmount = ($originalAmount * $discountRate) / 100;
        $finalAmount = $originalAmount - $discountAmount;

        Bill::updateOrCreate(
            [
                'student_id'       => $studentId,
                'fee_id'           => $fee->id,
                'academic_year_id' => $academicYearId,
                'period_name'      => $periodName,
            ],
            [
                'amount'           => $finalAmount,
                'original_amount'  => $originalAmount,
                'discount_amount'  => $discountAmount,
                'final_amount'     => $finalAmount,
                'due_date'         => $dueDate->toDateString(),
            ]
        );
    }
}