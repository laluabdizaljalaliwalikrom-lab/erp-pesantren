<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum FeeFrequency: string implements HasLabel, HasColor
{
    case MONTHLY = 'monthly';
    case SEMESTER = 'semester';
    case YEARLY = 'yearly';
    case INCIDENTAL = 'incidental';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MONTHLY => 'Bulanan',
            self::SEMESTER => 'Semesteran',
            self::YEARLY => 'Tahunan',
            self::INCIDENTAL => 'Insidental',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MONTHLY => 'success',
            self::SEMESTER => 'info',
            self::YEARLY => 'warning',
            self::INCIDENTAL => 'gray',
        };
    }
}
