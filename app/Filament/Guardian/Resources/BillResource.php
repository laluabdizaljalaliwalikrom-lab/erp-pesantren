<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources;

use App\Enums\BillStatus;
use App\Enums\PaymentStatus;
use App\Models\Bill;
use App\Models\Payment;
use App\Services\MidtransService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;
use Exception;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Tagihan';

    protected static ?string $modelLabel = 'Tagihan';

    protected static UnitEnum|string|null $navigationGroup = null;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['student', 'fee'])
            ->whereHas('student', fn (Builder $query) =>
                $query->where('guardian_id', auth('guardian')->id())
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('Nama Santri')
                    ->sortable(),

                TextColumn::make('fee.name')
                    ->label('Jenis Tagihan')
                    ->sortable(),

                TextColumn::make('period_name')
                    ->label('Periode')
                    ->sortable(),

                TextColumn::make('final_amount')
                    ->label('Total Tagihan')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('paid_amount_sum')
                    ->label('Sudah Dibayar')
                    ->money('IDR')
                    ->state(fn (Bill $record): float => (float) ($record->paid_amount_sum ?? 0))
                    ->sortable(),

                TextColumn::make('remaining')
                    ->label('Sisa Tagihan')
                    ->money('IDR')
                    ->state(fn (Bill $record): float => (float) ($record->final_amount - ($record->paid_amount_sum ?? 0)))
                    ->color(fn (float $state): string => $state > 0 ? 'danger' : 'success'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (BillStatus $state): string => match ($state) {
                        BillStatus::UNPAID  => 'danger',
                        BillStatus::PARTIAL => 'warning',
                        BillStatus::PAID    => 'success',
                        default             => 'gray',
                    })
                    ->formatStateUsing(fn (BillStatus $state): string => $state->getLabel())
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(BillStatus::class),
            ])
            ->actions([
                Action::make('pay')
                    ->label('Bayar Sekarang')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->visible(fn (Bill $record) => $record->status !== BillStatus::PAID)
                    ->action(function (Bill $record, MidtransService $midtransService): void {
                        $remaining = $record->final_amount - ($record->paid_amount_sum ?? 0);
                        
                        // Always pay full as per user instruction
                        $payment = Payment::create([
                            'bill_id' => $record->id,
                            'external_id' => 'PAY-' . substr($record->id, 0, 8) . '-' . time(),
                            'amount' => $remaining,
                            'method' => 'midtrans',
                            'status' => PaymentStatus::PENDING,
                            'user_id' => null, // Guardian payment
                        ]);

                        try {
                            $snapToken = $midtransService->createSnapToken($payment);
                            $payment->update(['snap_token' => $snapToken]);
                            
                            Notification::make()
                                ->title('Menyiapkan Pembayaran...')
                                ->success()
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->title('Gagal menyiapkan pembayaran')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    // We need to trigger the JS. Using action dispatch:
                    ->after(fn ($livewire, $record) => $record->payments()->latest()->first()?->snap_token 
                        ? $livewire->dispatch('open-midtrans-snap', snapToken: $record->payments()->latest()->first()->snap_token)
                        : null
                    ),

                Action::make('receipt')
                    ->label('Cetak Kuitansi')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->visible(fn (Bill $record) => $record->status === BillStatus::PAID)
                    ->url(fn (Bill $record): string => route('receipt.print', [
                        'payment' => $record->payments()->where('status', PaymentStatus::CONFIRMED)->latest()->first(),
                        'layout' => 'a4'
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->paginated([10, 25])
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('Belum Ada Tagihan')
            ->emptyStateDescription('Tidak ada tagihan untuk santri Anda saat ini.');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Guardian\Resources\BillResource\Pages\ListBills::route('/'),
        ];
    }
}
