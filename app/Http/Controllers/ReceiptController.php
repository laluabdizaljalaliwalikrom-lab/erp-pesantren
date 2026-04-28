<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceiptController extends Controller
{
    /** Allowed layout values. */
    private const LAYOUTS = ['a4', 'thermal'];

    /**
     * Stream a confirmed payment receipt as PDF.
     *
     * @param  string  $layout  'a4' | 'thermal'
     */
    public function print(Payment $payment, string $layout = 'a4'): Response|StreamedResponse
    {
        abort_if(
            $payment->status !== PaymentStatus::CONFIRMED,
            403,
            'Kwitansi hanya tersedia untuk pembayaran yang telah dikonfirmasi.'
        );

        abort_if(
            ! in_array($layout, self::LAYOUTS, strict: true),
            404,
            "Layout '{$layout}' tidak dikenal."
        );

        $payment->loadMissing([
            'bill.student',
            'bill.fee',
            'items.bill.student',
            'items.bill.fee',
        ]);

        // Resolve student: prefer items if bulk, fallback to bill
        $student = $payment->bill?->student ?? $payment->items->first()?->bill?->student;

        $pdf = Pdf::loadView("pdf.receipt-{$layout}", [
            'payment' => $payment,
            'student' => $student,
        ]);

        if ($layout === 'thermal') {
            $pdf->setPaper([0, 0, 158.00, 600]);
        } else {
            $pdf->setPaper('A4', 'portrait');
        }

        $pdf->setOptions([
            'dpi'                     => 150,
            'isHtml5ParserEnabled'    => true,
            'isRemoteEnabled'         => true,
            'defaultFont'             => 'DejaVu Sans Mono',
            'isFontSubsettingEnabled' => true,
        ]);

        $filename = sprintf(
            'kwitansi-%s-%s-%s.pdf',
            $layout,
            strtolower($student?->nis ?? 'unknown'),
            now()->format('Ymd-His')
        );

        return $pdf->stream($filename);
    }

    /**
     * Stream a batch of payments grouped by transaction_id as a thermal receipt.
     */
    public function printBatch(string $transactionId): Response|StreamedResponse
    {
        $payments = Payment::where('transaction_id', $transactionId)
            ->where('status', PaymentStatus::CONFIRMED)
            ->with(['bill.student', 'bill.fee', 'items.bill.student', 'items.bill.fee'])
            ->get();

        abort_if($payments->isEmpty(), 404, 'Transaksi tidak ditemukan.');

        $firstPayment = $payments->first();
        
        // Resolve student (either from single bill or bulk items)
        $student = $firstPayment->bill?->student ?? $firstPayment->items->first()?->bill?->student;

        $totalPaid = (float) $payments->sum('amount');
        $received = (float) ($firstPayment->amount_received ?? $totalPaid);
        $change = max(0, $received - $totalPaid);

        $pdf = Pdf::loadView('pdf.receipt-thermal-batch', [
            'settings' => settings(),
            'payments' => $payments,
            'payment' => $firstPayment, 
            'student' => $student,
            'transactionId' => $transactionId,
            'totalPaid' => $totalPaid,
            'received' => $received,
            'change' => $change,
            'date' => $firstPayment->created_at,
            'bendahara' => auth()->user()?->name ?? 'Petugas Keuangan',
        ]);

        $pdf->setPaper([0, 0, 158.00, 1000]); 
        
        $pdf->setOptions([
            'dpi'                     => 150,
            'isHtml5ParserEnabled'    => true,
            'isRemoteEnabled'         => true,
            'defaultFont'             => 'DejaVu Sans Mono',
        ]);

        return $pdf->stream("kwitansi-{$transactionId}.pdf");
    }
}
