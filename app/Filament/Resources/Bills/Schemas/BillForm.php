<?php

declare(strict_types=1);

namespace App\Filament\Resources\Bills\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('bill_number')
                    ->label('Nomor Tagihan')
                    ->disabled(),

                Select::make('student_id')
                    ->label('Santri')
                    ->relationship('student', 'full_name')
                    ->disabled(),

                Select::make('fee_id')
                    ->label('Jenis Tagihan')
                    ->relationship('fee', 'name')
                    ->disabled(),

                TextInput::make('final_amount')
                    ->label('Total Tagihan')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled(),

                TextInput::make('discount_amount')
                    ->label('Total Diskon')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled(),
                    
            ])->columns(2);
    }
}