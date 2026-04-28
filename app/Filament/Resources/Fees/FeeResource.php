<?php

declare(strict_types=1);

namespace App\Filament\Resources\Fees;

use App\Filament\Resources\Fees\Pages;
use App\Models\Fee;
use App\Enums\FeeFrequency;
use App\Filament\Resources\Fees\Tables;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;

class FeeResource extends Resource
{
    protected static ?string $model = Fee::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-dollar';
    
    public static function getNavigationLabel(): string
    {
        return 'Master Biaya';
    }

    public static function getModelLabel(): string
    {
        return 'Biaya';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Master Biaya';
    }
    protected static UnitEnum|string|null $navigationGroup = 'Keuangan';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return Schemas\FeeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Tables\FeesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFees::route('/'),
            'create' => Pages\CreateFee::route('/create'),
            'edit' => Pages\EditFee::route('/{record}/edit'),
        ];
    }
}
