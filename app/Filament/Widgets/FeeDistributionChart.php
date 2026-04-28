<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class FeeDistributionChart extends ChartWidget
{
    use HasWidgetShield;
    protected ?string $heading = 'Pemasukan per Kategori';
    protected ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = 6;
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Payment::query()
            ->join('bills', 'payments.bill_id', '=', 'bills.id')
            ->join('fees', 'bills.fee_id', '=', 'fees.id')
            ->where('payments.status', PaymentStatus::CONFIRMED)
            ->select('fees.name', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('fees.name')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Pemasukan',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#f97316'
                    ],
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
