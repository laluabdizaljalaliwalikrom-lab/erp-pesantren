<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Exception;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized', true);
        Config::$is3ds = config('services.midtrans.is_3ds', true);
    }

    /**
     * Create Snap Token for a payment.
     *
     * @throws Exception
     */
    public function createSnapToken(Payment $payment): string
    {
        $guardian = null;
        $studentNames = [];
        $items = [];
        $totalAmount = 0;

        // Check if this is a bulk payment using payment_items
        if ($payment->items()->exists()) {
            foreach ($payment->items as $item) {
                $bill = $item->bill;
                $student = $bill->student;
                
                if (!$guardian) {
                    $guardian = $student->guardian;
                }
                
                if ($student && !in_array($student->full_name, $studentNames)) {
                    $studentNames[] = $student->full_name;
                }
                
                $items[] = [
                    'id' => $bill->id,
                    'price' => (int) $item->amount,
                    'quantity' => 1,
                    'name' => 'Tagihan: ' . $bill->fee->name . ' (' . ($bill->period_name ?? 'Sekali Bayar') . ')',
                ];
                $totalAmount += (int) $item->amount;
            }
        } else {
            // Fallback for single bill payment
            $bill = $payment->bill;
            $student = $bill?->student;
            $guardian = $student?->guardian;
            
            if ($student) {
                $studentNames[] = $student->full_name;
            }
            
            $items[] = [
                'id' => $bill?->id ?? '0',
                'price' => (int) $payment->amount,
                'quantity' => 1,
                'name' => 'Tagihan: ' . ($bill?->fee?->name ?? 'Pembayaran') . ' (' . ($bill?->period_name ?? 'Sekali Bayar') . ')',
            ];
            $totalAmount = (int) $payment->amount;
        }

        $customerFirstName = !empty($studentNames) ? implode(', ', $studentNames) : ($guardian->name ?? 'Santri');
        // Midtrans has a limit for first_name, usually around 255 chars, but best to keep it reasonable
        if (strlen($customerFirstName) > 100) {
            $customerFirstName = substr($customerFirstName, 0, 97) . '...';
        }

        $params = [
            'transaction_details' => [
                'order_id' => $payment->external_id,
                'gross_amount' => (int) ($totalAmount ?: $payment->amount),
            ],
            'customer_details' => [
                'first_name' => $customerFirstName,
                'email' => filter_var($guardian?->email ?? '', FILTER_VALIDATE_EMAIL) ? $guardian->email : 'wali@example.com',
                'phone' => $guardian?->phone_number ?? '',
            ],
            'item_details' => count($items) > 0 ? $items : [
                [
                    'id' => '0',
                    'price' => (int) $payment->amount,
                    'quantity' => 1,
                    'name' => 'Pembayaran Tagihan',
                ]
            ],
            'enabled_payments' => [
                'credit_card', 'mandiri_clickpay', 'cimb_clicks',
                'bca_klikbca', 'bca_klikpay', 'bri_epay', 'echannel', 'permata_va',
                'bca_va', 'binter_va', 'other_va', 'gopay', 'indomaret',
                'danamon_online', 'akulaku', 'shopeepay'
            ],
        ];

        return Snap::getSnapToken($params);
    }

    /**
     * Verify and parse Midtrans notification.
     *
     * @return Notification
     * @throws Exception
     */
    public function getNotification(): Notification
    {
        return new Notification();
    }
}