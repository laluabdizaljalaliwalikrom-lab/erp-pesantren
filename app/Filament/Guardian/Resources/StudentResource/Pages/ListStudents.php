<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\StudentResource\Pages;

use App\Filament\Guardian\Resources\StudentResource;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
