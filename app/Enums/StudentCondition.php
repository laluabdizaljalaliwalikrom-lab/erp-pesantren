<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StudentCondition: string implements HasLabel
{
    case NONE = 'none';
    case YATIM = 'yatim';
    case PIATU = 'piatu';
    case YATIM_PIATU = 'yatim_piatu';
    case KURANG_MAMPU = 'kurang_mampu';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NONE => 'Reguler',
            self::YATIM => 'Yatim',
            self::PIATU => 'Piatu',
            self::YATIM_PIATU => 'Yatim Piatu',
            self::KURANG_MAMPU => 'Kurang Mampu',
        };
    }

    /**
     * Get discount rate in percentage.
     */
    public function getDiscountRate(): int
    {
        return match ($this) {
            self::NONE => 0,
            self::YATIM => 50,
            self::PIATU => 50,
            self::YATIM_PIATU => 100,
            self::KURANG_MAMPU => 30,
        };
    }
}
