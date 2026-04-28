<?php

declare(strict_types=1);

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('expense_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('amount')
                    ->label('Jumlah Pengeluaran')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->extraInputAttributes(['style' => 'text-align: right']),

                DatePicker::make('date')
                    ->label('Tanggal')
                    ->default(now())
                    ->required(),

                Textarea::make('description')
                    ->label('Keterangan')
                    ->required()
                    ->columnSpanFull(),

                FileUpload::make('attachment')
                    ->label('Lampiran')
                    ->directory('expenses')
                    ->image()
                    ->maxSize(2048)
                    ->imageEditor(),

                Select::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'CASH' => 'Tunai',
                        'TRANSFER' => 'Transfer Bank',
                    ])
                    ->default('CASH'),
            ])->columns(2);
    }
}
