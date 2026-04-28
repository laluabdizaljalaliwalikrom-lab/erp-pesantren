<?php

declare(strict_types=1);

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\AlumniResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlumni extends EditRecord
{
    protected static string $resource = AlumniResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
