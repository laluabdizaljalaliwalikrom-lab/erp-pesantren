<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Number;

class LatestPaymentsWidget extends BaseWidget
{
    protected static ?string $heading = 'Transaksi Terakhir (Berhasil)';
    protected static ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = 6;
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->where('status', PaymentStatus::CONFIRMED)
                    ->latest()
                    ->limit(4)
            )
            ->columns([
                Tables\Columns\TextColumn::make('bill.student.full_name')
                    ->label('Santri')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id'),

                Tables\Columns\TextColumn::make('method')
                    ->label('Metode')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'midtrans' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => $state === 'cash' ? 'Tunai' : 'Bank'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m H:i')
                    ->description(fn (Payment $record): string => $record->created_at->diffForHumans()),
            ])
            ->paginated(false);
    }
}
