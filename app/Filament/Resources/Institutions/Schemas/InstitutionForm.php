<?php

declare(strict_types=1);

namespace App\Filament\Resources\Institutions\Schemas;

use App\Enums\InstitutionType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InstitutionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nama Lembaga')
                ->required()
                ->maxLength(255),

            Select::make('type')
                ->label('Tipe Lembaga')
                ->options(InstitutionType::class)
                ->required(),
        ]);
    }
}