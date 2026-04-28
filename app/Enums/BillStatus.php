<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BillStatus: string implements HasLabel, HasColor
{
    case UNPAID = 'unpaid';
    case PARTIAL = 'partial';
    case PAID = 'paid';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UNPAID => 'Belum Bayar',
            self::PARTIAL => 'Cicilan',
            self::PAID => 'Lunas',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::UNPAID => 'danger',
            self::PARTIAL => 'warning',
            self::PAID => 'success',
        };
    }
}
