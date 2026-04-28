<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasLabel, HasColor
{
    case PENDING   = 'pending';
    case CONFIRMED = 'confirmed';
    case REJECTED  = 'rejected';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING   => 'Menunggu',
            self::CONFIRMED => 'Dikonfirmasi',
            self::REJECTED  => 'Ditolak',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING   => 'warning',
            self::CONFIRMED => 'success',
            self::REJECTED  => 'danger',
        };
    }
}
