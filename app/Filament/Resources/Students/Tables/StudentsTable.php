<?php

declare(strict_types=1);

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Models\Student;
use App\Filament\Resources\Students\Schemas\StudentView;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Enums\StudentCondition;
use App\Enums\StudentStatus;
use App\Enums\Gender;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['guardian', 'institution']))
            ->columns([
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->disk('public'),

                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): string => $record->nisn ?? 'No NISN'),

                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('gender')
                    ->label('L/P')
                    ->sortable(),

                TextColumn::make('guardian.name')
                    ->label('Wali')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('special_condition')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (StudentCondition $state): string => match ($state) {
                        StudentCondition::YATIM => 'warning',
                        StudentCondition::PIATU => 'warning',
                        StudentCondition::YATIM_PIATU => 'danger',
                        StudentCondition::KURANG_MAMPU => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options(StudentStatus::class),

                SelectFilter::make('gender')
                    ->label('Filter Gender')
                    ->options(Gender::class),

                SelectFilter::make('residency_status')
                    ->label('Filter Mukim')
                    ->options([
                        'mukim' => 'Mukim',
                        'tidak_mukim' => 'Non-Mukim',
                    ]),
            ])
            ->recordActions([
                Action::make('view_profile')
                    ->label('Lihat Profil')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->infolist(fn (Student $record, $infolist) => StudentView::configure($infolist))
                    ->modalHeading('Profil Santri')
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
