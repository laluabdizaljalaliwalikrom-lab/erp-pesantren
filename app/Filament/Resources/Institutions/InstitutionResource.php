<?php

declare(strict_types=1);

namespace App\Filament\Resources\Institutions;

use App\Models\Institution;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class InstitutionResource extends Resource
{
    protected static ?string $model = Institution::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static UnitEnum|string|null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Lembaga';

    protected static ?string $pluralModelLabel = 'Daftar Lembaga';

    public static function form(Schema $schema): Schema
    {
        return Schemas\InstitutionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Tables\InstitutionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInstitutions::route('/'),
            'create' => Pages\CreateInstitution::route('/create'),
            'edit'   => Pages\EditInstitution::route('/{record}/edit'),
        ];
    }
}