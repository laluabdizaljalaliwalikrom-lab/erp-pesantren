<?php

declare(strict_types=1);

namespace App\Filament\Resources\Classes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('institution_id')
                ->label('Lembaga')
                ->relationship('institution', 'name')
                ->required()
                ->searchable()
                ->preload(),
                
            TextInput::make('name')
                ->label('Nama Kelas')
                ->required()
                ->maxLength(255),
        ]);
    }
}