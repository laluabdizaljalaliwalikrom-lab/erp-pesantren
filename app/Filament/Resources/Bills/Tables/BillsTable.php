<?php

declare(strict_types=1);

namespace App\Filament\Resources\Bills\Tables;

use App\Enums\BillStatus;
use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Bill;
use App\Models\Institution;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use App\Services\WhatsappService;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

class BillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['student.guardian', 'fee']))
            ->defaultSort('created_at', 'desc')
            ->poll('5s')
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('Nama Santri')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('fee.name')
                    ->label('Jenis Biaya')
                    ->sortable(),

                TextColumn::make('period_name')
                    ->label('Periode')
                    ->sortable(),

                TextColumn::make('final_amount')
                    ->label('Total Tagihan')
                    ->money('IDR')
                    ->summarize(Sum::make()->money('IDR')->label('Total'))
                    ->sortable(),

                TextColumn::make('paid_amount_sum')
                    ->label('Sudah Dibayar')
                    ->money('IDR')
                    ->state(fn (Bill $record): float => (float) ($record->paid_amount_sum ?? 0))
                    ->sortable(),

                TextColumn::make('remaining_balance')
                    ->label('Sisa Hutang')
                    ->money('IDR')
                    ->state(fn (Bill $record): float => (float) ($record->final_amount - ($record->paid_amount_sum ?? 0)))
                    ->color(fn (float $state): string => $state > 0 ? 'danger' : 'success')
                    ->summarize(
                        Summarizer::make()
                            ->label('Total Sisa')
                            ->using(function ($query): string {
                                $totalFinal = (float) $query->sum('final_amount');
                                $totalPaid = (float) \App\Models\Payment::query()
                                    ->whereIn('bill_id', $query->select('id'))
                                    ->where('status', \App\Enums\PaymentStatus::CONFIRMED)
                                    ->sum('amount');

                                return Number::currency($totalFinal - $totalPaid, 'IDR');
                            })
                    )
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (BillStatus $state): string => match ($state) {
                        BillStatus::UNPAID => 'danger',
                        BillStatus::PARTIAL => 'warning',
                        BillStatus::PAID => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (BillStatus $state): string => $state->getLabel())
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options(BillStatus::class),

                SelectFilter::make('institution')
                    ->label('Lembaga')
                    ->relationship('student.academicRecords.institution', 'name'),

                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                
                Action::make('record_payment')
                    ->label('Bayar')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->url(fn (Bill $record): string => PaymentResource::getUrl('create', [
                        'bill_id' => $record->id,
                        'student_id' => $record->student_id,
                        'amount' => $record->final_amount - ($record->paid_amount_sum ?? 0),
                    ]))
                    ->visible(fn (Bill $record): bool => $record->status !== BillStatus::PAID),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('sendWaReminder')
                        ->label('Kirim Pengingat WA')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $successCount = 0;
                            $failCount = 0;
                            $noPhoneCount = 0;

                            foreach ($records as $record) {
                                $guardianPhone = $record->student?->guardian?->phone_number;

                                if (empty($guardianPhone)) {
                                    $noPhoneCount++;
                                    continue;
                                }

                                $studentName = $record->student?->full_name ?? 'Santri';
                                $guardianName = $record->student?->guardian?->name ?? 'Bapak/Ibu';
                                $feeName = $record->fee?->name ?? 'Uang Sekolah';
                                $remainingBalance = (float) ($record->final_amount - ($record->paid_amount_sum ?? 0));
                                $formattedBalance = Number::currency($remainingBalance, 'IDR');

                                $message = "*PENGINGAT ADMINISTRASI PESANTREN*\n" .
                                    "--------------------------------------------\n" .
                                    "Assalamu'alaikum Warahmatullahi Wabarakatuh,\n\n" .
                                    "Yth. Bapak/Ibu *" . $record->student->guardian?->name . "*,\n" .
                                    "Wali dari ananda *" . $record->student->full_name . "*\n\n" .
                                    "Semoga Bapak/Ibu senantiasa dalam keadaan sehat dan penuh keberkahan.\n\n" .
                                    "Kami dari Bagian Bendahara ingin menginformasikan rincian tagihan *" . $record->fee->name . "* yang saat ini masih tercatat di sistem kami:\n\n" .
                                    "▪️ *Rincian Biaya:* " . $record->fee->name . "\n" .
                                    "▪️ *Periode:* " . ($record->period_name ?? '-') . "\n" .
                                    "▪️ *Sisa Tunggakan:* " . \Illuminate\Support\Number::currency((float)$record->remaining_balance, 'IDR') . "\n\n" .
                                    "Mohon bantuannya untuk melakukan penyelesaian administrasi tersebut demi kelancaran kegiatan belajar mengajar ananda di Pesantren.\n\n" .
                                    "Jika Bapak/Ibu sudah melakukan pembayaran, mohon abaikan pesan ini atau kirimkan bukti bayar kepada kami.\n\n" .
                                    "Jazakumullah khairan katsiran.\n" .
                                    "Wassalamu'alaikum Warahmatullahi Wabarakatuh.\n\n" .
                                    "*Bendahara Pesantren*";

                                if (WhatsappService::send($guardianPhone, $message)) {
                                    $successCount++;
                                } else {
                                    $failCount++;
                                }
                            }

                            if ($noPhoneCount > 0) {
                                Notification::make()
                                    ->title('Nomor HP Tidak Ditemukan')
                                    ->body("{$noPhoneCount} santri tidak memiliki nomor HP wali.")
                                    ->warning()
                                    ->send();
                            }

                            Notification::make()
                                ->title('Proses Antrean WhatsApp Selesai')
                                ->body("{$successCount} Pesan dikirim, {$failCount} Gagal.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}