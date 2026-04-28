<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\PaymentWebhookController;

// URL untuk melihat daftar tagihan (GET)
Route::get('/bills', [BillingController::class, 'index']);

// URL untuk men-generate tagihan massal (POST)
Route::post('/billing-events/{billing_event_id}/generate', [BillingController::class, 'generateMassBilling']);

// URL untuk Webhook Midtrans (Lama - Deprecated)
Route::post('/payment/notification', [PaymentWebhookController::class, 'handleNotification']);

// URL Webhook Midtrans (Baru - POS & Batch)
Route::post('/midtrans/webhook', [\App\Http\Controllers\MidtransWebhookController::class, 'handle'])->name('api.midtrans.webhook');