<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class IncomeTrendChart extends ChartWidget
{
    use HasWidgetShield;
    protected ?string $heading = 'Tren Pemasukan (30 Hari Terakhir)';
    protected ?string $pollingInterval = '30s';
    protected string $color = 'success';
    protected int | string | array $columnSpan = 6;
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $days = 29;
        $startDate = now()->subDays($days)->startOfDay();

        // 1. Fetch raw daily sums from DB
        $rawValues = Payment::query()
            ->where('status', PaymentStatus::CONFIRMED)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as aggregate')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('aggregate', 'date');

        $labels = [];
        $data = [];

        // 2. Loop through every day in the range to ensure a continuous timeline (filling gaps with 0)
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M');
            $data[] = (float) ($rawValues[$date] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pemasukan (IDR)',
                    'data' => $data,
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
