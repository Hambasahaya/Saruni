<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function notify(string $type, string $title, string $body, array $payload = [], array $recipientIds = []): void
    {
        Log::info('Notification dispatched', [
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'payload' => $payload,
            'recipients' => $recipientIds,
        ]);
    }
}
