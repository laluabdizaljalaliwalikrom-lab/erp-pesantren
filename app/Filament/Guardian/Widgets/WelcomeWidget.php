<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected string $view = 'filament.guardian.widgets.welcome';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 1;
}
