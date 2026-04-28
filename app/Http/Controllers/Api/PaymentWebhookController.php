<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Notification;
use App\Jobs\SendPaymentSuccessNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Throwable;

class PaymentWebhookController extends Controller
{
    /**
     * Handle incoming notifications from Midtrans.
     */
    public function handleNotification(Request $request): JsonResponse
    {
        // Setup Midtrans Configuration for the Notification class
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);

        try {
            $notif = new \Midtrans\Notification();
        } catch (Throwable $e) {
            Log::error('Midtrans Webhook Invalid Signature:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $transactionStatus = $notif->transaction_status;
        $orderId = $notif->order_id;

        Log::info('Midtrans Webhook Received:', [
            'order_id' => $orderId,
            'status'   => $transactionStatus,
        ]);

        try {
            DB::transaction(function () use ($orderId, $transactionStatus) {
                // lockForUpdate() ensures that overlapping webhook calls won't cause race conditions
                $bill = Bill::where('id', $orderId)->lockForUpdate()->first();

                if (!$bill) {
                    return;
                }

                if (in_array($transactionStatus, ['settlement', 'capture'], true)) {
                    // Only update if it hasn't already been marked as paid
                    if ($bill->status !== 'paid') {
                        $bill->update([
                            'status'      => 'paid',
                            'paid_amount' => $bill->final_amount,
                        ]);

                        // Create a notification record and Dispatch Job
                        $message = "Terima kasih, pembayaran tagihan untuk ananda {$bill->student->full_name} telah berhasil kami terima.";
                        $notification = Notification::create([
                            'type'    => 'whatsapp',
                            'message' => $message,
                            'status'  => 'queued',
                        ]);

                        SendPaymentSuccessNotification::dispatch($notification, '081234567890')->afterCommit();
                    }
                } elseif ($transactionStatus === 'pending') {
                    // Keep it as unpaid
                    Log::info("Midtrans Webhook: Order ID {$orderId} is pending.");
                } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)) {
                    Log::error("Midtrans Webhook Failure: Order ID {$orderId} failed with status {$transactionStatus}.");
                }
            });

            return response()->json(['message' => 'Webhook processed successfully'], 200);
        } catch (Throwable $e) {
            Log::error('Midtrans Webhook DB Error:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}