<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService
    ) {}

    /**
     * Handle Midtrans notifications.
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $notification = $request->all();
            
            $orderId = $notification['order_id'] ?? null;
            $statusCode = $notification['status_code'] ?? null;
            $grossAmount = $notification['gross_amount'] ?? null;
            $serverKey = config('services.midtrans.server_key');
            
            // Validate signature for security
            $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
            
            if ($signature !== ($notification['signature_key'] ?? '')) {
                Log::error('Midtrans Webhook: Invalid Signature', ['payload' => $notification]);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $payment = Payment::where('external_id', $orderId)->first();

            if (!$payment) {
                Log::error('Midtrans Webhook: Payment not found', ['order_id' => $orderId]);
                return response()->json(['message' => 'Payment not found'], 404);
            }

            $transactionStatus = $notification['transaction_status'];
            $type = $notification['payment_type'] ?? 'midtrans';

            Log::info('Midtrans Webhook: Processing', [
                'order_id' => $orderId,
                'status' => $transactionStatus
            ]);

            if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                $payment->update([
                    'status' => PaymentStatus::CONFIRMED,
                    'method' => $type,
                    'reference_id' => $notification['transaction_id'] ?? $payment->reference_id,
                ]);
                
                // Bill status is handled by Payment model booted() static saved() method
            } elseif ($transactionStatus === 'pending') {
                $payment->update(['status' => PaymentStatus::PENDING]);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $payment->update(['status' => PaymentStatus::REJECTED]);
            }

            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            Log::error('Midtrans Webhook: Error', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
