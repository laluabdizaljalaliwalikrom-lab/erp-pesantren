<?php

declare(strict_types=1);

namespace App\Filament\Resources\Academics;

use App\Filament\Resources\Academics\Pages;
use App\Models\StudentAcademic;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class AcademicResource extends Resource
{
    protected static ?string $model = StudentAcademic::class;

    // Strict Typing for PHP 8.3 & Filament v4
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-academic-cap';

    public static function getNavigationLabel(): string
    {
        return 'Akademik Santri';
    }

    public static function getModelLabel(): string
    {
        return 'Akademik Santri';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Akademik Santri';
    }

    protected static UnitEnum|string|null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return Schemas\AcademicForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Tables\AcademicsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademics::route('/'),
            'create' => Pages\CreateAcademic::route('/create'),
            'edit' => Pages\EditAcademic::route('/{record}/edit'),
        ];
    }
}