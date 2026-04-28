<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ResidencyStatus: string implements HasLabel
{
    case MUKIM = 'mukim';
    case TIDAK_MUKIM = 'tidak_mukim';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MUKIM       => 'Mukim',
            self::TIDAK_MUKIM => 'Tidak Mukim',
        };
    }
}
