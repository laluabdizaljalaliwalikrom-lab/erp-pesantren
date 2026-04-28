<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use App\Enums\BillStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Record a payment for a bill and update its status.
     *
     * @param string $billId
     * @param array $data
     * @param bool $allowOverpayment
     * @return Payment
     * @throws ValidationException
     */
    public function recordPayment(string $billId, array $data, bool $allowOverpayment = false): Payment
    {
        return DB::transaction(function () use ($billId, $data, $allowOverpayment) {
            $bill = Bill::findOrFail($billId);

            $amount = (float) $data['amount'];

            // 1. Validation: Capacity check
            if (!$allowOverpayment && $amount > $bill->remaining_balance) {
                throw ValidationException::withMessages([
                    'amount' => [sprintf('Payment amount (%.2f) exceeds remaining balance (%.2f).', $amount, $bill->remaining_balance)],
                ]);
            }

            // 2. Create Payment Record
            $payment = Payment::create([
                'bill_id'        => $bill->id,
                'amount'         => $amount,
                'method'         => $data['method'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'status'         => 'success',
                'payment_date'   => $data['payment_date'] ?? now()->toDateString(),
            ]);

            // 3. Recalculate Bill Status
            $this->updateBillStatus($bill);

            return $payment;
        });
    }

    /**
     * Recalculate and update the status of a bill based on its payments.
     *
     * @param Bill $bill
     * @return void
     */
    public function updateBillStatus(Bill $bill): void
    {
        $totalPaid = $bill->total_paid;
        $finalAmount = (float) $bill->final_amount;

        if ($totalPaid >= $finalAmount) {
            $bill->status = BillStatus::PAID;
        } elseif ($totalPaid > 0) {
            $bill->status = BillStatus::PARTIAL;
        } else {
            $bill->status = BillStatus::UNPAID;
        }

        $bill->save();
    }
}
