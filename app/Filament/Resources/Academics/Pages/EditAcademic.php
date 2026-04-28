<?php

declare(strict_types=1);

namespace App\Filament\Resources\Academics\Pages;

use App\Filament\Resources\Academics\AcademicResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcademic extends EditRecord
{
    protected static string $resource = AcademicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}