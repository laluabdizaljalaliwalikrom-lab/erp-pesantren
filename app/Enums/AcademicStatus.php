<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AcademicStatus: string implements HasLabel, HasColor
{
    case ACTIVE = 'active';
    case GRADUATED = 'graduated';
    case MOVED = 'moved';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::GRADUATED => 'Graduated',
            self::MOVED => 'Moved',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::GRADUATED => 'info',
            self::MOVED => 'gray',
        };
    }
}
