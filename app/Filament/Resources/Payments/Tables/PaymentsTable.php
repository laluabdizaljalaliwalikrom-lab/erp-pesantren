<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Tables;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Services\WhatsappService;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\URL;

class PaymentsTable
{
    /**
     * Configure the table.
     */
    public static function get(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['bill.fee', 'student.guardian', 'user']))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal Transaksi')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('bill.student.full_name')
                    ->label('Santri')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Kasir')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('bill.fee.name')
                    ->label('Jenis Biaya')
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('method')
                    ->label('Metode')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash'     => 'success',
                        'transfer' => 'info',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash'     => 'Tunai',
                        'transfer' => 'Transfer',
                        default    => $state,
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (PaymentStatus $state): string|array|null => $state->getColor())
                    ->formatStateUsing(fn (PaymentStatus $state): string => $state->getLabel()),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Filter Kasir')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Action::make('confirm')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Payment $record): bool => $record->status === PaymentStatus::PENDING)
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->modalDescription('Apakah Anda yakin ingin mengkonfirmasi pembayaran ini?')
                    ->action(function (Payment $record): void {
                        $record->status = PaymentStatus::CONFIRMED;
                        $record->saveQuietly();
                        $record->bill?->refreshStatus();

                        // Automated WhatsApp Receipt
                        $guardianPhone = $record->bill?->student?->guardian?->phone_number;
                        if ($guardianPhone) {
                            $guardianName = $record->bill?->student?->guardian?->name ?? 'Bapak/Ibu';
                            $studentName = $record->bill?->student?->full_name ?? 'Ananda';
                            $feeName = $record->bill?->fee?->name ?? 'Pembayaran';
                            $amount = Number::currency((float) $record->amount, 'IDR');
                            $publicUrl = URL::to('/receipt/share/' . $record->id);

                            $message = "Alhamdulillah, pembayaran telah kami terima! 🙏\n\n" .
                                "Yth. Bapak/Ibu {$guardianName}, kami menginformasikan bahwa pembayaran {$feeName} untuk ananda {$studentName} sebesar {$amount} telah berhasil kami verifikasi.\n\n" .
                                "Simpan kwitansi digital Anda melalui tautan berikut:\n{$publicUrl}\n\n" .
                                "Jazakumullah khairan katsiran.\n-- Bendahara Pesantren --";

                            WhatsappService::send($guardianPhone, $message);
                        }

                        Notification::make()
                            ->title('Pembayaran Dikonfirmasi')
                            ->body('Status pembayaran berhasil dikonfirmasi dan kwitansi telah dikirim via WhatsApp.')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Payment $record): bool => $record->status === PaymentStatus::PENDING)
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->placeholder('Opsional — masukkan alasan penolakan')
                            ->rows(3),
                    ])
                    ->action(function (Payment $record, array $data): void {
                        $record->status = PaymentStatus::REJECTED;

                        if (filled($data['rejection_reason'] ?? null)) {
                            $record->notes = $data['rejection_reason'];
                        }

                        $record->saveQuietly();
                        $record->bill?->refreshStatus();

                        Notification::make()
                            ->title('Pembayaran Ditolak')
                            ->body('Status pembayaran telah ditolak.')
                            ->danger()
                            ->send();
                    }),

                ActionGroup::make([
                    Action::make('print_a4')
                        ->label('Cetak A4')
                        ->icon('heroicon-o-document-text')
                        ->color('gray')
                        ->url(fn (Payment $record): string => route('receipt.print', ['payment' => $record, 'layout' => 'a4']))
                        ->openUrlInNewTab(),

                    Action::make('print_thermal')
                        ->label('Cetak Thermal (58mm)')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->url(fn (Payment $record): string => route('receipt.print', ['payment' => $record, 'layout' => 'thermal']))
                        ->openUrlInNewTab(),
                ])
                ->label('Cetak Kwitansi')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->visible(fn (Payment $record): bool => $record->status === PaymentStatus::CONFIRMED),
            ]);
    }
}
