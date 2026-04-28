<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Pages;

use App\Filament\Guardian\Widgets\LatestAnnouncementsWidget;
use App\Filament\Guardian\Widgets\MyChildrenWidget;
use App\Filament\Guardian\Widgets\UnpaidBillsWidget;
use App\Filament\Guardian\Widgets\WelcomeWidget;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

class GuardianDashboard extends BaseDashboard
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Beranda';

    protected static ?string $navigationLabel = 'Beranda';

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm'      => 1,
            'md'      => 3,
            'xl'      => 3,
        ];
    }

    public function getWidgets(): array
    {
        return [
            LatestAnnouncementsWidget::class,
            WelcomeWidget::class,
            MyChildrenWidget::class,
            UnpaidBillsWidget::class,
        ];
    }
}
