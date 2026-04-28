<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    public static function send(string $target, string $message): bool
    {
        $token = env('FONNTE_TOKEN');
        
        // Pembersihan nomor HP
        $target = preg_replace('/^08/', '628', $target);

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->post('https://api.fonnte.com/send', [
            'target' => $target,
            'message' => $message,
        ]);

        // CATAT KE LOG: Supaya kita tahu apa yang terjadi
        Log::info('Fonnte Request ke: ' . $target);
        Log::info('Fonnte Response: ' . $response->body());

        $result = $response->json();

        // Cek apakah status di dalam JSON itu true
        if ($response->successful() && isset($result['status']) && $result['status'] === true) {
            return true;
        }

        return false;
    }
}