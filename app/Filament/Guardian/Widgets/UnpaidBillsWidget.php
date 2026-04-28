<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Bill;
use App\Models\Student;
use Filament\Widgets\Widget;
use Illuminate\Support\Number;

class UnpaidBillsWidget extends Widget
{
    protected string $view = 'filament.guardian.widgets.unpaid-bills';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 3;

    public function getStats(): array
    {
        $studentIds = Student::query()
            ->where('guardian_id', auth('guardian')->id())
            ->pluck('id');

        $bills = Bill::query()
            ->with('student')
            ->whereIn('student_id', $studentIds)
            ->get();

        $totalFinal = $bills->sum('final_amount');

        $totalPaid = \App\Models\Payment::query()
            ->whereIn('bill_id', $bills->pluck('id'))
            ->where('status', PaymentStatus::CONFIRMED)
            ->sum('amount');

        $totalRemaining = $totalFinal - $totalPaid;
        $unpaidCount = $bills->filter(fn ($b) => ($b->final_amount - 0) > 0)->count();

        return [
            'total_remaining' => (float) $totalRemaining,
            'total_paid'      => (float) $totalPaid,
            'bills_count'     => $bills->count(),
            'unpaid_count'    => $unpaidCount,
            'is_clear'        => $totalRemaining <= 0,
        ];
    }
}
