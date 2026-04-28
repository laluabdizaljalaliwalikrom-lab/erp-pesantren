<?php

declare(strict_types=1);

namespace App\Filament\Resources\Institutions\Tables;

use App\Enums\InstitutionType;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InstitutionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lembaga')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipe Lembaga')
                    ->badge()
                    ->color(fn (InstitutionType $state): string => match ($state) {
                        InstitutionType::FORMAL     => 'primary',
                        InstitutionType::NON_FORMAL => 'success',
                    })
                    ->sortable(),
            ])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}