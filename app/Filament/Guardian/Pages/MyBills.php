<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Pages;

use App\Enums\BillStatus;
use App\Enums\PaymentStatus;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Student;
use App\Services\MidtransService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class MyBills extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    protected string $view = 'filament.guardian.pages.my-bills';

    protected static ?string $title = 'Billing & Pembayaran';

    protected static ?string $navigationLabel = 'Tagihan';

    protected static ?string $slug = 'tagihan';

    public Collection $students;
    public ?string $activeStudentId = null;
    public array $selectedBillIds = [];

    public function mount(): void
    {
        $this->students = Student::query()
            ->where('guardian_id', auth('guardian')->id())
            ->with(['academic', 'academic.schoolClass', 'academic.institution'])
            ->get();

        if ($this->students->isNotEmpty()) {
            $this->activeStudentId = (string) $this->students->first()->id;
        }
    }

    public function getBillsProperty()
    {
        if (!$this->activeStudentId) {
            return collect();
        }

        return Bill::query()
            ->where('student_id', $this->activeStudentId)
            ->where('status', '!=', BillStatus::PAID)
            ->with(['fee'])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function toggleSelection(string $billId): void
    {
        if (in_array($billId, $this->selectedBillIds)) {
            $this->selectedBillIds = array_diff($this->selectedBillIds, [$billId]);
        } else {
            $this->selectedBillIds[] = $billId;
        }
    }

    public function getTotalSelectedProperty(): float
    {
        return (float) Bill::query()
            ->whereIn('id', $this->selectedBillIds)
            ->get()
            ->sum('remaining_balance');
    }

    public function checkout(MidtransService $midtransService): void
    {
        if (empty($this->selectedBillIds)) {
            Notification::make()->title('Pilih setidaknya satu tagihan')->warning()->send();
            return;
        }

        try {
            DB::beginTransaction();

            // Cleanup old pending payments for these specific bills to avoid "stacking"
            $oldPendingPayments = Payment::where('status', PaymentStatus::PENDING)
                ->whereHas('items', fn($q) => $q->whereIn('bill_id', $this->selectedBillIds))
                ->get();

            foreach ($oldPendingPayments as $oldPayment) {
                $oldPayment->update(['status' => PaymentStatus::REJECTED]);
            }

            $total = $this->total_selected;
            
            // Create Payment with TRX- prefix
            $payment = Payment::create([
                'external_id' => 'TRX-' . time() . '-' . rand(100, 999),
                'amount' => $total,
                'method' => 'midtrans',
                'status' => PaymentStatus::PENDING,
                'user_id' => null, 
            ]);

            // Create Payment Items
            foreach ($this->selectedBillIds as $billId) {
                $bill = Bill::find($billId);
                PaymentItem::create([
                    'payment_id' => $payment->id,
                    'bill_id' => $billId,
                    'amount' => $bill->remaining_balance,
                ]);
            }

            $snapToken = $midtransService->createSnapToken($payment->load('items.bill.student.guardian'));
            $payment->update(['snap_token' => $snapToken]);

            DB::commit();

            $this->dispatch('open-midtrans-snap', snapToken: $snapToken);
            
            Notification::make()->title('Menyiapkan gerbang pembayaran...')->success()->send();

        } catch (Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Gagal memproses pembayaran')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function setActiveStudent(string $id): void
    {
        $this->activeStudentId = $id;
        // Keep selected bills if they want to pay across children? 
        // prompt said "bulk payments for multiple children", 
        // so I won't clear selectedBillIds when switching tabs.
    }
}
