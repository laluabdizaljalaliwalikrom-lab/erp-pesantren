<?php

declare(strict_types=1);

namespace App\Filament\Resources\Classes;

use App\Models\SchoolClass;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class ClassResource extends Resource
{
    protected static ?string $model = SchoolClass::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static UnitEnum|string|null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Kelas';

    protected static ?string $pluralModelLabel = 'Daftar Kelas';

    public static function form(Schema $schema): Schema
    {
        return Schemas\ClassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Tables\ClassesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListClasses::route('/'),
            'create' => Pages\CreateClass::route('/create'),
            'edit'   => Pages\EditClass::route('/{record}/edit'),
        ];
    }
}