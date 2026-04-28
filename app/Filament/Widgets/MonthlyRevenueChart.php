<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class MonthlyRevenueChart extends ChartWidget
{
    use HasWidgetShield;
    protected ?string $heading = 'Trend Pendapatan Bulanan (Tahun Ini)';
    protected int | string | array $columnSpan = 6;
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $currentYear = now()->year;

        // Fetch monthly confirmed revenue for current year
        $monthlyRevenue = Payment::query()
            ->where('status', PaymentStatus::CONFIRMED)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Prepare data for all 12 months
        $data = [];
        $labels = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create($currentYear, $i)->translatedFormat('M');
            $labels[] = $monthName;
            $data[] = (float) ($monthlyRevenue[$i] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendapatan (IDR)',
                    'data' => $data,
                    'fill' => 'start',
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.5)', // Slightly transparent for bar chart
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
