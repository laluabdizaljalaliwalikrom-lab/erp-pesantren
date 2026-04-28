<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;

class PaymentNotificationService
{
    /**
     * Send payment success notification to the student's guardian.
     *
     * @param Collection<int, Payment> $payments Collection of payment models (must be eager-loaded with 'bill.fee' and 'bill.student.guardian')
     * @param float $change Optional change amount (for Cash payments)
     */
    public function sendPaymentSuccess(Collection $payments, float $change = 0): void
    {
        if ($payments->isEmpty()) {
            return;
        }

        $totalAllocatedSum = 0.0;
        $feeLines = [];
        $student = null;
        $paymentMethod = $payments->first()->method; // Assuming all payments in a batch share the same method

        foreach ($payments as $payment) {
            // Case 1: Multiple items in one payment (Bulk)
            if ($payment->items()->exists()) {
                foreach ($payment->items as $item) {
                    $totalAllocatedSum += (float) $item->amount;
                    $bill = $item->bill;
                    if (!$student && $bill && $bill->student) {
                        $student = $bill->student;
                    }
                    if ($bill) {
                        $feeLines[] = $this->formatFeeLine($bill, (float) $item->amount);
                    }
                }
            } 
            // Case 2: Single bill linked directly to the payment (Legacy/Single)
            elseif ($payment->bill) {
                $totalAllocatedSum += (float) $payment->amount;
                $bill = $payment->bill;
                if (!$student && $bill && $bill->student) {
                    $student = $bill->student;
                }
                $feeLines[] = $this->formatFeeLine($bill, (float) $payment->amount);
            }
        }

        if (!$student || !$student->guardian?->phone_number) {
            Log::warning('Payment Notification: Missing guardian phone number.', [
                'student_id' => $student?->id,
                'transaction_id' => $payments->first()->transaction_id
            ]);
            return;
        }

        $guardianPhone = $student->guardian->phone_number;
        $studentName = $student->full_name;
        $nis = $student->nis ?? '-';
        $classList = $student->academicRecords->map(fn($ar) => $ar->schoolClass?->name)->filter()->unique()->implode(', ') ?: 'Tanpa Kelas';
        $formattedTotal = Number::currency($totalAllocatedSum, 'IDR', 'id');
        $paymentDate = now()->format('d/m/Y H:i');
        $paymentMethodLabel = ($paymentMethod === 'midtrans') ? 'Midtrans (QRIS/VA)' : 'Tunai (Cash)';
        $orderId = $payments->first()->transaction_id;

        $message = "*Assalamu'alaikum Warahmatullahi Wabarakatuh*\n\n" .
            "*BUKTI PEMBAYARAN DIGITAL*\n" .
            "*Pesantren Syaikh Abdurrahman*\n\n" .
            "Alhamdulillah, telah kami terima pembayaran atas nama:\n" .
            "👤 *Nama Santri:* {$studentName}\n" .
            "🆔 *NIS:* {$nis}\n" .
            "🏫 *Kelas:* {$classList}\n\n" .
            "*RINCIAN TRANSAKSI:*\n" .
            "🗓️ *Tanggal:* {$paymentDate}\n" .
            "💳 *Metode:* {$paymentMethodLabel}\n" .
            "🧾 *No. Ref:* {$orderId}\n\n" .
            "*ALOKASI PEMBAYARAN:*";

        foreach ($feeLines as $line) {
            $message .= "\n{$line}";
        }

        $message .= "\n\n💰 *TOTAL DITERIMA: {$formattedTotal}*\n\n" .
            "Jazakumullah Khairan Katsiran atas kepercayaan Bapak/Ibu. Semoga menjadi amal jariyah dan keberkahan bagi ananda dalam menuntut ilmu. Aamiin Ya Rabbal 'Alamin.\n\n" .
            "Hormat kami,\n" .
            "*Bendahara Pesantren*\n\n" .
            "_(Pesan ini dibuat otomatis oleh sistem, tidak perlu dibalas)_";

        if ($change > 0) {
            $formattedChange = Number::currency($change, 'IDR', 'id');
            $message .= "\n\n*NB:* Uang kembalian sebesar *{$formattedChange}* telah diserahkan.";
        }

        try {
            $success = WhatsappService::send($guardianPhone, $message);
            
            if ($success) {
                Log::info('Payment Notification: WhatsApp sent successfully.', [
                    'target' => $guardianPhone,
                    'method' => $paymentMethod,
                    'transaction_id' => $payments->first()->transaction_id
                ]);
            } else {
                Log::error('Payment Notification: WhatsApp delivery failed.', [
                    'target' => $guardianPhone,
                    'transaction_id' => $payments->first()->transaction_id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Payment Notification: System error during sending.', [
                'error' => $e->getMessage(),
                'transaction_id' => $payments->first()->transaction_id
            ]);
        }
    }

    /**
     * Send professional billing statement to the student's guardian.
     *
     * @param \App\Models\Student $student
     * @param Collection<\App\Models\Bill> $bills
     */
    public function sendBillingStatement(\App\Models\Student $student, Collection $bills): bool
    {
        if ($bills->isEmpty() || !$student->guardian?->phone_number) {
            return false;
        }

        $studentName = $student->full_name;
        $nis = $student->nis ?? '-';
        $classList = $student->academicRecords->map(fn($ar) => $ar->schoolClass?->name)->filter()->unique()->implode(', ') ?: 'Tanpa Kelas';
        $totalUnpaid = $bills->sum('remaining_balance');
        $formattedTotal = Number::currency($totalUnpaid, 'IDR', 'id');
        $date = now()->format('d/m/Y H:i');

        $feeLines = [];
        foreach ($bills as $bill) {
            $feeName = $bill->fee?->name ?? 'Tagihan';
            $period = $bill->period_name ? "({$bill->period_name})" : '';
            $remaining = Number::currency((float) $bill->remaining_balance, 'IDR', 'id');
            $feeLines[] = "• {$feeName} {$period}: *{$remaining}*";
        }

        $message = "*Assalamu'alaikum Warahmatullahi Wabarakatuh*\n\n" .
            "*PEMBERITAHUAN TAGIHAN DIGITAL*\n" .
            "*Pesantren Syaikh Abdurrahman*\n\n" .
            "Yth. *Bapak/Ibu {$student->guardian->name}*,\n" .
            "Wali dari ananda: *{$studentName}*\n\n" .
            "Melalui pesan ini, kami menginformasikan rincian tagihan pendidikan yang belum lunas per tanggal *{$date}* sebagai berikut:\n\n" .
            "👤 *Nama Santri:* {$studentName}\n" .
            "🆔 *NIS:* {$nis}\n" .
            "🏫 *Kelas:* {$classList}\n\n" .
            "*RINCIAN TAGIHAN AKTIF:*";

        foreach ($feeLines as $line) {
            $message .= "\n{$line}";
        }

        $message .= "\n\n⚠️ *TOTAL TUNGGAKAN: {$formattedTotal}*\n\n" .
            "Mohon Bapak/Ibu kiranya dapat segera melakukan pelunasan melalui loket Bendahara atau via transfer. \n\n" .
            "Semoga Allah memberkahi harta dan keluarga kita semua. Jazakumullah Khairan Katsiran.\n\n" .
            "Hormat kami,\n" .
            "*Bendahara Pesantren*\n\n" .
            "_(Pesan ini dibuat otomatis oleh sistem, tidak perlu dibalas)_";

        try {
            $success = WhatsappService::send($student->guardian->phone_number, $message);
            
            if ($success) {
                Log::info('Billing Notification: WhatsApp sent successfully.', [
                    'target' => $student->guardian->phone_number,
                    'student_id' => $student->id
                ]);
                return true;
            } else {
                Log::error('Billing Notification: WhatsApp delivery failed.', [
                    'target' => $student->guardian->phone_number,
                    'student_id' => $student->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Billing Notification: System error during sending.', [
                'error' => $e->getMessage(),
                'student_id' => $student->id
            ]);
        }

        return false;
    }

    /**
     * Helper to format a single fee line for WhatsApp.
     */
    private function formatFeeLine(\App\Models\Bill $bill, float $amount): string
    {
        $feeName = $bill->fee?->name ?? 'Tagihan';
        $period = $bill->period_name ? "({$bill->period_name})" : '';
        $statusText = $bill->remaining_balance <= 0 ? 'LUNAS' : 'CICIL';
        $formattedAllocated = Number::currency($amount, 'IDR', 'id');

        return "• {$feeName} {$period}: *{$formattedAllocated}* ({$statusText})";
    }
}
