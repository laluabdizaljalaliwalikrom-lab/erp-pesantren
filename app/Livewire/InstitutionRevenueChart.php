<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class InstitutionRevenueChart extends ChartWidget
{
    protected ?string $heading = 'Institution Revenue Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
