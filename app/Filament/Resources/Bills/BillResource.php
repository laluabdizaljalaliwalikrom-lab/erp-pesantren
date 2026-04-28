<?php

declare(strict_types=1);

namespace App\Filament\Resources\Bills;

use App\Filament\Resources\Bills\Pages;
use App\Models\Bill;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    // Strict Typing for PHP 8.3 & Filament v4
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Daftar Tagihan (Piutang)';
    
    protected static ?string $modelLabel = 'Tagihan';

    protected static UnitEnum|string|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withSum([
                'payments as paid_amount_sum' => fn ($query) => $query->where('status', \App\Enums\PaymentStatus::CONFIRMED)
            ], 'amount');
    }

    public static function form(Schema $schema): Schema
    {
        return Schemas\BillForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Tables\BillsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBills::route('/'),
            'view'  => Pages\ViewBill::route('/{record}'),
        ];
    }
}