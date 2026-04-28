<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments;

use App\Filament\Resources\Payments\Pages;
use App\Models\Payment;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static UnitEnum|string|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', \App\Enums\PaymentStatus::PENDING)->count();
    }

    /**
     * Configure the form schema.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components(Schemas\PaymentForm::get());
    }

    /**
     * Configure the table.
     */
    public static function table(Table $table): Table
    {
        return Tables\PaymentsTable::get($table);
    }

    public static function getRelations(): array
    {
        return [
            \AlizHarb\ActivityLog\RelationManagers\ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
