<?php

declare(strict_types=1);

namespace App\Filament\Resources\ExpenseCategories;

use App\Filament\Resources\ExpenseCategories\Pages;
use App\Models\ExpenseCategory;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;
use Illuminate\Database\Eloquent\Builder;

class ExpenseCategoryResource extends Resource
{
    protected static ?string $model = ExpenseCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Kategori Pengeluaran';
    
    protected static ?string $modelLabel = 'Kategori Pengeluaran';

    protected static UnitEnum|string|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return Schemas\ExpenseCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Tables\ExpenseCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListExpenseCategories::route('/'),
            'create' => Pages\CreateExpenseCategory::route('/create'),
            'edit'   => Pages\EditExpenseCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletingScope::class,
            ]);
    }
}
