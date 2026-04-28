<?php

declare(strict_types=1);

namespace App\Filament\Resources\Fees\Schemas;

use App\Enums\FeeFrequency;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Biaya')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('amount')
                    ->label('Nominal')
                    ->numeric()
                    ->required(),
                    
                Select::make('frequency')
                    ->label('Frekuensi')
                    ->options(FeeFrequency::class)
                    ->required(),
            ]);
    }
}
