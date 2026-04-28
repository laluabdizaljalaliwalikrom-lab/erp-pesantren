<?php

declare(strict_types=1);

namespace App\Filament\Resources\Expenses;

use App\Filament\Resources\Expenses\Pages;
use App\Models\Expense;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;
use Illuminate\Database\Eloquent\Builder;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationLabel(): string
    {
        return 'Pengeluaran';
    }

    public static function getModelLabel(): string
    {
        return 'Pengeluaran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Pengeluaran';
    }

    protected static UnitEnum|string|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['category']);
    }

    public static function form(Schema $schema): Schema
    {
        return Schemas\ExpenseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Tables\ExpensesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit'   => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
