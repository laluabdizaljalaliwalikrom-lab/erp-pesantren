<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Riwayat Pembayaran';

    protected static ?string $modelLabel = 'Pembayaran';

    protected static UnitEnum|string|null $navigationGroup = null;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['bill.student', 'bill.fee'])
            ->whereHas('bill.student', fn (Builder $query) =>
                $query->where('guardian_id', auth('guardian')->id())
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('bill.student.full_name')
                    ->label('Nama Santri')
                    ->sortable(),

                TextColumn::make('bill.fee.name')
                    ->label('Jenis Tagihan')
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Jumlah Bayar')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (PaymentStatus $state): string => match ($state) {
                        PaymentStatus::CONFIRMED => 'success',
                        PaymentStatus::PENDING   => 'warning',
                        PaymentStatus::REJECTED  => 'danger',
                        default                  => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Bayar')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(PaymentStatus::class),
            ])
            ->paginated([10, 25])
            ->emptyStateIcon('heroicon-o-banknotes')
            ->emptyStateHeading('Belum Ada Riwayat Pembayaran')
            ->emptyStateDescription('Tidak ada riwayat pembayaran untuk santri Anda saat ini.');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Guardian\Resources\PaymentResource\Pages\ListPayments::route('/'),
        ];
    }
}
