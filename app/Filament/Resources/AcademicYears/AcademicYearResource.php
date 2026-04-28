<?php

declare(strict_types=1);

namespace App\Filament\Resources\AcademicYears;

use App\Models\AcademicYear;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static UnitEnum|string|null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Tahun Ajaran';
    }

    public static function getModelLabel(): string
    {
        return 'Tahun Ajaran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Tahun Ajaran';
    }

    public static function form(Schema $schema): Schema
    {
        return Schemas\AcademicYearForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Tables\AcademicYearTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAcademicYears::route('/'),
            'create' => Pages\CreateAcademicYear::route('/create'),
            'edit'   => Pages\EditAcademicYear::route('/{record}/edit'),
        ];
    }
}