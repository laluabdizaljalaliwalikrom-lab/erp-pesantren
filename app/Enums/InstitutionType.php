<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum InstitutionType: string implements HasLabel
{
    case FORMAL = 'FORMAL';
    case NON_FORMAL = 'NON_FORMAL';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FORMAL     => 'Formal (SMP/SMA)',
            self::NON_FORMAL => 'Non-Formal (MDT/Tahfidz)',
        };
    }
}