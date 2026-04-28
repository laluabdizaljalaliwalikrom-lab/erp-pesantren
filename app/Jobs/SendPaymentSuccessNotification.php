<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Notification;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendPaymentSuccessNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Notification $notification,
        public string $targetNumber
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        try {
            $success = $whatsAppService->sendMessage($this->targetNumber, $this->notification->message);

            if ($success) {
                $this->notification->update(['status' => 'sent']);
            } else {
                $this->notification->update(['status' => 'failed']);
            }
        } catch (Throwable $e) {
            $this->notification->update(['status' => 'failed']);
        }
    }
}