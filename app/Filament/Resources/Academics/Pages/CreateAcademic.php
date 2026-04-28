<?php

declare(strict_types=1);

namespace App\Filament\Resources\Academics\Pages;

use App\Filament\Resources\Academics\AcademicResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademic extends CreateRecord
{
    protected static string $resource = AcademicResource::class;
}