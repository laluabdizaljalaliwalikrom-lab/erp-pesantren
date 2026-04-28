<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Schemas;

use App\Models\Bill;
use App\Models\Student;
use App\Enums\BillStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Number;

class PaymentForm
{
    /**
     * Get the form schema components.
     *
     * @return array
     */
    public static function get(): array
    {
        return [
            Select::make('student_id')
                ->label('Student')
                ->options(Student::query()->where('status', 'active')->pluck('full_name', 'id'))
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('bill_id', null))
                ->required()
                ->default(fn () => request()->query('student_id')),

            Select::make('bill_id')
                ->label('Bill')
                ->options(function (Get $get) {
                    $studentId = $get('student_id');
                    if (!$studentId) {
                        return [];
                    }

                    return Bill::query()
                        ->where('student_id', $studentId)
                        ->whereIn('status', [BillStatus::UNPAID, BillStatus::PARTIAL])
                        ->with('fee')
                        ->get()
                        ->mapWithKeys(fn (Bill $bill) => [
                            $bill->id => sprintf(
                                '%s - %s (Sisa: %s)',
                                $bill->fee->name,
                                $bill->period_name,
                                Number::format((float) $bill->remaining_balance)
                            )
                        ]);
                })
                ->searchable()
                ->preload()
                ->live()
                ->required()
                ->default(fn () => request()->query('bill_id')),

            Placeholder::make('bill_info')
                ->label('Informasi Tagihan')
                ->content(function (Get $get) {
                    $billId = $get('bill_id');
                    if (!$billId) {
                        return 'Silahkan pilih tagihan untuk melihat informasi saldo.';
                    }

                    $bill = Bill::find($billId);
                    if (!$bill) {
                        return 'Tagihan tidak ditemukan.';
                    }

                    return sprintf(
                        'Total Tagihan: %s | Belum dibayar: %s',
                        Number::currency((float) ($bill->final_amount ?? 0), 'IDR'),
                        Number::currency((float) ($bill->remaining_balance ?? 0), 'IDR')
                    );
                })
                ->columnSpanFull(),

            TextInput::make('amount')
                ->label('Jumlah Bayar')
                ->numeric()
                ->prefix('IDR')
                ->required()
                ->rules([
                    fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                        $billId = $get('bill_id');
                        if (!$billId) return;

                        $bill = Bill::find($billId);
                        if (!$bill) return;

                        if ($value > $bill->remaining_balance) {
                            $fail("Jumlah bayar tidak boleh melebihi sisa saldo (" . Number::format((float) $bill->remaining_balance) . ").");
                        }
                    },
                ])
                ->default(fn () => request()->query('amount')),

            Select::make('method')
                ->label('Metode')
                ->options([
                    'cash'     => 'Tunai (Cash)',
                    'transfer' => 'Transfer Bank',
                ])
                ->default('cash')
                ->required(),

            TextInput::make('transaction_id')
                ->label('ID Transaksi')
                ->placeholder('Opsional untuk Transfer'),

            DatePicker::make('payment_date')
                ->label('Tanggal Bayar')
                ->default(now())
                ->required(),
        ];
    }
}
