<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\BillStatus;
use App\Enums\PaymentStatus;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Expense;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class FinancialStatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;

    protected function getColumns(): int | array
    {
        return [
            'default' => 1,
            'md' => 2,
            'lg' => 4,
        ];
    }

    protected function formatCurrency(float $value): string
    {
        if ($value >= 1_000_000_000) {
            return 'Rp ' . number_format($value / 1_000_000_000, 1, ',', '.') . ' M';
        }

        if ($value >= 1_000_000) {
            return 'Rp ' . number_format($value / 1_000_000, 1, ',', '.') . ' jt';
        }

        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    protected function getStats(): array
    {
        // 1. Pemasukan Hari Ini (Confirmed Only)
        $incomeToday = (float) Payment::where('status', PaymentStatus::CONFIRMED)
            ->whereDate('created_at', today())
            ->sum('amount');

        // 2. Saldo Tunai (Cash in Hand)
        $cashInHand = (float) Payment::where('status', PaymentStatus::CONFIRMED)
            ->where('method', 'cash')
            ->sum('amount');

        // 3. Saldo Bank (Midtrans Settlements & Mapped Methods)
        $bankBalance = (float) Payment::where('status', PaymentStatus::CONFIRMED)
            ->whereNotIn('method', ['cash', 'transfer'])
            ->sum('amount');

        // 4. Total Piutang (Total Receivables)
        $totalExpected = (float) Bill::sum('final_amount');
        $totalReceived = (float) Payment::where('status', PaymentStatus::CONFIRMED)->sum('amount');
        $totalReceivables = max(0, $totalExpected - $totalReceived);

        $cardClasses = 'cursor-pointer hover:bg-gray-50 transition ' .
                      '[&_.fi-wi-stats-overview-stat-value]:text-lg [&_.fi-wi-stats-overview-stat-value]:md:text-xl [&_.fi-wi-stats-overview-stat-value]:font-bold [&_.fi-wi-stats-overview-stat-value]:tracking-tight ' .
                      '[&_.fi-wi-stats-overview-stat-label]:text-xs [&_.fi-wi-stats-overview-stat-label]:uppercase';

        return [
            Stat::make('Pemasukan Hari Ini', $this->formatCurrency($incomeToday))
                ->description('Total pembayaran hari ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->extraAttributes([
                    'class' => $cardClasses,
                ]),

            Stat::make('Saldo Tunai', $this->formatCurrency($cashInHand))
                ->description('Total pembayaran via Cash')
                ->descriptionIcon('heroicon-m-wallet')
                ->icon('heroicon-o-wallet')
                ->color('info')
                ->extraAttributes([
                    'class' => $cardClasses,
                ]),

            Stat::make('Saldo Bank', $this->formatCurrency($bankBalance))
                ->description('Total pembayaran via Midtrans')
                ->descriptionIcon('heroicon-m-credit-card')
                ->icon('heroicon-o-credit-card')
                ->color('primary')
                ->extraAttributes([
                    'class' => $cardClasses,
                ]),

            Stat::make('Total Piutang', $this->formatCurrency($totalReceivables))
                ->description('Tagihan santri belum lunas')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->extraAttributes([
                    'class' => $cardClasses,
                ]),
        ];
    }
}
