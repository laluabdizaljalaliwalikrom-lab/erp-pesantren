<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\BillStatus;
use App\Enums\PaymentStatus;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Student;
use App\Services\WhatsappService;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\ImageEntry;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\TextSize;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use BackedEnum;

class StudentFinance extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Checkout Pembayaran';

    protected string $view = 'filament.pages.student-finance';

    // ─── STATE ──────────────────────────────────────────────────────
    public ?string $student_id = null;
    public ?Student $student = null;

    /** @var array<int, string> Selected bill UUIDs, synced from Blade checkboxes */
    public array $selected_bills = [];

    /** @var array<string, mixed> Form state for the right sidebar */
    public ?array $data = [];

    public ?string $receiptUrl = null;
    public ?string $snapToken = null;

    /** @var Collection<string, Collection<int, Bill>>|null Cached bills */
    protected ?Collection $cached_grouped_bills = null;

    /** @var array<string, float> Map of bill ID to remaining balance */
    public array $bill_amounts = [];

    /** @var array<string, string> Map of bill ID to fee name */
    public array $bill_names = [];

    /** @var array<string, string> Map of bill ID to due date */
    public array $bill_dates = [];

    // ─── MOUNT ──────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->student_id = request()->query('student_id');

        if (! $this->student_id) {
            $this->redirect(StudentSearch::getUrl());
            return;
        }

        $this->student = Student::with(['academicRecords.institution'])->find($this->student_id);

        if (! $this->student) {
            $this->redirect(StudentSearch::getUrl());
            return;
        }

        // Pre-cache bill data for Alpine.js
        $unpaidBills = $this->getUnpaidBills();
        $this->bill_amounts = $unpaidBills->pluck('remaining_balance', 'id')->map(fn($v) => (float)$v)->toArray();
        $this->bill_dates = $unpaidBills->pluck('due_date', 'id')->map(fn($v) => $v?->toDateString() ?? '')->toArray();
        $this->bill_names = $unpaidBills->mapWithKeys(function($bill) {
            return [$bill->id => ($bill->fee?->name ?? 'Tagihan') . ($bill->period_name ? " ($bill->period_name)" : '')];
        })->toArray();

        $this->form->fill([
            'payment_method' => 'cash',
            'amount_received' => 0,
        ]);
    }

    // ─── STUDENT BIODATA INFOLIST ───────────────────────────────────

    public function studentInfolist(Schema $infolist): Schema
    {
        $totalDebt = $this->student->bills()
            ->whereIn('status', [BillStatus::UNPAID, BillStatus::PARTIAL])
            ->get()
            ->sum(fn ($bill) => $bill->remaining_balance);

        return $infolist
            ->record($this->student)
            ->schema([
                Section::make('Biodata Santri')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(5)
                            ->schema([
                                ImageEntry::make('photo')
                                    ->hiddenLabel()
                                    ->disk('public')
                                    ->circular()
                                    ->height(80)
                                    ->extraImgAttributes(['class' => 'ring-2 ring-gray-100 dark:ring-white/10 shadow-sm'])
                                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=' . urlencode($this->student->full_name) . '&color=FFFFFF&background=0284c7'),

                                TextEntry::make('full_name')
                                    ->label('Nama Lengkap')
                                    ->weight('bold')
                                    ->size(TextSize::Large),

                                TextEntry::make('nis')
                                    ->label('NIS'),

                                TextEntry::make('academicRecords.schoolClass.name')
                                    ->label('Kelas')
                                    ->badge()
                                    ->placeholder('Belum Masuk Kelas'),

                                TextEntry::make('total_debt')
                                    ->label('Total Tunggakan')
                                    ->state(Number::currency((float) $totalDebt, 'IDR', 'id'))
                                    ->badge()
                                    ->color('danger')
                                    ->size(TextSize::Large),
                            ]),
                    ]),
            ]);
    }

    // ─── DATA LOGIC: GROUPED BILLS ──────────────────────────────────

    /**
     * Retrieve unpaid/partial bills grouped by fee name, sorted by due_date.
     *
     * @return Collection<string, Collection<int, Bill>>
     */
    public function getGroupedBills(): Collection
    {
        if ($this->cached_grouped_bills !== null) {
            return $this->cached_grouped_bills;
        }

        return $this->cached_grouped_bills = Bill::where('student_id', $this->student_id)
            ->whereIn('status', [BillStatus::UNPAID, BillStatus::PARTIAL])
            ->with(['fee'])
            ->orderBy('due_date')
            ->get()
            ->groupBy(fn (Bill $bill): string => $bill->fee?->name ?? 'Lainnya');
    }

    /**
     * Get a flat collection of all unpaid bills (for calculation helpers).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Bill>
     */
    private function getUnpaidBills(): \Illuminate\Database\Eloquent\Collection
    {
        return Bill::where('student_id', $this->student_id)
            ->whereIn('status', [BillStatus::UNPAID, BillStatus::PARTIAL])
            ->with(['fee'])
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Sum of remaining_balance for currently selected bills.
     */
    public function getSelectedTotal(): float
    {
        if (empty($this->selected_bills)) {
            return 0.0;
        }

        return (float) $this->getGroupedBills()
            ->flatten()
            ->whereIn('id', $this->selected_bills)
            ->sum(fn ($bill) => (float) $bill->remaining_balance);
    }

    // ─── CHECKBOX TOGGLE METHODS (Backend Sync) ─────────────────────

    public function toggleBill(string $billId): void
    {
        if (in_array($billId, $this->selected_bills, true)) {
            $this->selected_bills = array_values(
                array_filter($this->selected_bills, fn (string $id): bool => $id !== $billId)
            );
        } else {
            $this->selected_bills[] = $billId;
        }
    }

    public function toggleGroupBills(string $feeName): void
    {
        $groupBillIds = $this->getGroupedBills()
            ->get($feeName, collect())
            ->pluck('id')
            ->toArray();

        $allSelected = count(array_intersect($this->selected_bills, $groupBillIds)) === count($groupBillIds);

        if ($allSelected) {
            $this->selected_bills = array_values(
                array_filter($this->selected_bills, fn (string $id): bool => ! in_array($id, $groupBillIds, true))
            );
        } else {
            $this->selected_bills = array_values(
                array_unique(array_merge($this->selected_bills, $groupBillIds))
            );
        }
    }

    public function toggleAllBills(): void
    {
        $allBillIds = $this->getGroupedBills()->flatten()->pluck('id')->toArray();

        if (count($this->selected_bills) === count($allBillIds)) {
            $this->selected_bills = [];
        } else {
            $this->selected_bills = $allBillIds;
        }
    }

    // ─── DATA LOGIC: PAYMENT HISTORY ──────────────────────────────

    public function getPaymentHistory(): Collection
    {
        return Payment::query()
            ->where(function ($query) {
                $query->whereHas('items.bill', fn($q) => $q->where('student_id', $this->student_id))
                    ->orWhereHas('bill', fn($q) => $q->where('student_id', $this->student_id));
            })
            ->where('status', '!=', PaymentStatus::REJECTED)
            ->with(['items.bill.fee', 'bill.fee'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    // ─── UNIFIED POS FORM (Split Layout) ────────────────────────────

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Flex::make([
                    // ─── LEFT PANEL: Tabs (Active Bills & History) ─
                    Tabs::make('POS_Left_Panel')
                        ->tabs([
                            Tab::make('Tagihan Aktif')
                                ->icon('heroicon-o-banknotes')
                                ->schema([
                                    Section::make('Tagihan Aktif')
                                        ->icon('heroicon-o-clipboard-document-list')
                                        ->description('Tandai tagihan yang akan dibayarkan pada sesi ini.')
                                        ->headerActions([
                                            Action::make('print_statement')
                                                ->label('Cetak Rincian')
                                                ->icon('heroicon-m-printer')
                                                ->color('gray')
                                                ->url(fn () => route('student.statement', ['student' => $this->student_id]))
                                                ->openUrlInNewTab(),

                                            Action::make('send_wa_billing')
                                                ->label('Kirim ke WA')
                                                ->icon('heroicon-o-chat-bubble-left-right')
                                                ->color('success')
                                                ->requiresConfirmation()
                                                ->modalHeading('Kirim Rincian Tagihan')
                                                ->modalDescription('Apakah Anda yakin ingin mengirim rincian tagihan pendidikan ke WhatsApp wali santri?')
                                                ->action(function (\App\Services\PaymentNotificationService $notificationService) {
                                                    $bills = \App\Models\Bill::where('student_id', $this->student_id)
                                                        ->whereIn('status', [\App\Enums\BillStatus::UNPAID, \App\Enums\BillStatus::PARTIAL])
                                                        ->with(['fee', 'student.academicRecords.schoolClass', 'student.guardian'])
                                                        ->orderBy('due_date')
                                                        ->get();

                                                    if ($bills->isEmpty()) {
                                                        Notification::make()->warning()->title('Tidak Ada Tagihan')->body('Santri ini tidak memiliki tagihan aktif yang perlu dikirim.')->send();
                                                        return;
                                                    }

                                                    $success = $notificationService->sendBillingStatement($this->student, $bills);

                                                    if ($success) {
                                                        Notification::make()->success()->title('Pesan Terkirim')->body('Rincian tagihan telah berhasil dikirim ke WhatsApp wali santri.')->send();
                                                    } else {
                                                        Notification::make()->danger()->title('Gagal Mengirim')->body('Pesan gagal dikirim. Pastikan nomor HP wali sudah terdaftar.')->send();
                                                    }
                                                }),
                                        ])
                                        ->schema([
                                            View::make('filament.components.bill-table'),
                                        ]),
                                ]),

                            Tab::make('Riwayat Pembayaran')
                                ->icon('heroicon-o-clock')
                                ->schema([
                                    Section::make('Riwayat Pembayaran Terakhir')
                                        ->icon('heroicon-o-clock')
                                        ->description('Daftar transaksi yang sudah diproses.')
                                        ->schema([
                                            View::make('filament.components.payment-history-table'),
                                        ]),
                                ]),
                        ])
                        ->grow(),

                    // ─── RIGHT PANEL: Calculator Sidebar ────────────
                    Group::make([
                        Section::make('Ringkasan Pembayaran')
                            ->icon('heroicon-o-calculator')
                            ->schema([
                                Hidden::make('payment_method'),
                                Hidden::make('amount_received'),
                                View::make('filament.components.pos-calculator'),

                                Actions::make([
                                    Action::make('back')
                                        ->label('Kembali')
                                        ->color('gray')
                                        ->icon('heroicon-o-arrow-left')
                                        ->url(fn () => StudentSearch::getUrl()),

                                    Action::make('process')
                                        ->label(fn (Get $get) => $get('payment_method') === 'midtrans' ? 'Generate Link Pembayaran' : 'Bayar & Cetak')
                                        ->color(fn (Get $get) => $get('payment_method') === 'midtrans' ? 'primary' : 'success')
                                        ->icon(fn (Get $get) => $get('payment_method') === 'midtrans' ? 'heroicon-o-qr-code' : 'heroicon-o-check-circle')
                                        ->extraAttributes(['type' => 'submit']),
                                ])->columns(2),
                            ]),
                    ])->grow(false),
                ])->from('md'),
            ])
            ->statePath('data');
    }

    // ─── PROCESS PAYMENT (Waterfall Logic) ──────────────────────────

    public function processPayment(
        \App\Services\MidtransService $midtransService,
        \App\Services\PaymentNotificationService $notificationService
    ): void {
        $data = $this->form->getState();

        $paymentMethod = $data['payment_method'] ?? 'cash';
        $amountReceived = $paymentMethod === 'midtrans' ? $this->getSelectedTotal() : (float) ($data['amount_received'] ?? 0);

        if (empty($this->selected_bills)) {
            Notification::make()->warning()->title('Keranjang Kosong')->body('Pilih minimal satu tagihan.')->send();
            return;
        }

        if ($amountReceived <= 0) {
            Notification::make()->warning()->title('Nominal Tidak Valid')->send();
            return;
        }

        // Fetch selected bills SORTED BY due_date for waterfall allocation
        $selectedBills = Bill::whereIn('id', $this->selected_bills)
            ->with(['fee', 'student.guardian', 'student.academicRecords.schoolClass'])
            ->orderBy('due_date')
            ->get();

        $balanceToDistribute = $amountReceived;
        $transactionId = 'POS-' . strtoupper(uniqid());
        $externalId = 'TRX-POS-' . time() . '-' . rand(100, 999);
        $totalAllocatedSum = 0.0;
        
        DB::beginTransaction();

        try {
            // Create a SINGLE main Payment record for the entire session
            $mainPayment = Payment::create([
                'external_id' => $externalId,
                'bill_id' => $selectedBills->first()->id, // Reference to the first bill for relationship tracking
                'amount' => $amountReceived,
                'amount_received' => $amountReceived,
                'method' => $paymentMethod,
                'status' => $paymentMethod === 'midtrans' ? PaymentStatus::PENDING : PaymentStatus::CONFIRMED,
                'transaction_id' => $transactionId,
                'payment_date' => now(),
                'user_id' => auth()->id(),
            ]);

            foreach ($selectedBills as $bill) {
                if ($balanceToDistribute <= 0) break;

                $remaining = (float) $bill->remaining_balance;
                $allocated = min($balanceToDistribute, $remaining);
                $balanceToDistribute -= $allocated;
                $totalAllocatedSum += $allocated;

                // Create individual payment item linked to the main payment
                // The Payment model's saved hook will automatically trigger bill->refreshStatus()
                PaymentItem::create([
                    'payment_id' => $mainPayment->id,
                    'bill_id' => $bill->id,
                    'amount' => $allocated,
                ]);
            }

            DB::commit();

            if ($paymentMethod === 'midtrans') {
                // Use the unified createSnapToken that supports items
                $snapToken = $midtransService->createSnapToken($mainPayment->load('items.bill.student.guardian'));
                $mainPayment->update(['snap_token' => $snapToken]);
                
                $this->dispatch('open-midtrans-snap', snapToken: $snapToken);

                Notification::make()
                    ->success()
                    ->title('Sistem Siap')
                    ->body('Menyiapkan gerbang pembayaran...')
                    ->send();

                // Reset state
                $this->selected_bills = [];
                $this->form->fill();
                return;
            }

            // Standard Cash Flow
            $this->receiptUrl = route('receipt.print', ['payment' => $mainPayment->id, 'layout' => 'a4']);
            $this->mountAction('receiptPreview');

            Notification::make()->success()->title('Pembayaran Berhasil')->body('Transaksi telah dicatat dan tagihan diperbarui.')->send();

            // Notify via WA (Optional)
            $mainPayment->load(['items.bill.fee', 'items.bill.student.guardian', 'bill.fee', 'bill.student.guardian']);
            $notificationService->sendPaymentSuccess(collect([$mainPayment]), (float) $balanceToDistribute);

            // Reset state
            $this->selected_bills = [];
            $this->form->fill();

        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()->danger()->title('Sistem Error')->body($e->getMessage())->send();
        }
    }

    // ─── ACTIONS ────────────────────────────────────────────────────

    public function receiptPreviewAction(): Action
    {
        return Action::make('receiptPreview')
            ->modalHeading('Pratinjau Kwitansi')
            ->modalWidth('4xl')
            ->modalContent(fn () => view('filament.modals.receipt-preview', [
                'url' => $this->receiptUrl,
            ]))
            ->modalSubmitActionLabel('Cetak')
            ->modalCancelActionLabel('Tutup')
            ->action(fn () => $this->dispatch('print-receipt'));
    }
}
