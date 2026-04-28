<?php

declare(strict_types=1);

namespace App\Filament\Resources\Academics\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AcademicsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('Nama Santri')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('institution.name')
                    ->label('Lembaga')
                    ->badge()
                    ->sortable(),

                TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->sortable(),

                TextColumn::make('semester')
                    ->label('Semester')
                    ->sortable(),

                TextColumn::make('schoolClass.name')
                    ->label('Kelas')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->filters([
                TrashedFilter::make(), // Added to support SoftDeletes requirement
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}