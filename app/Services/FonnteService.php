<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    /**
     * Send a WhatsApp message to a specific number using Fonnte API.
     *
     * @param string $phone
     * @param string $message
     * @return bool
     */
    public static function sendWhatsappNotification(string $phone, string $message): bool
    {
        $token = config('services.fonnte.token');

        if (!$token || $token === 'your-fonnte-token-here') {
            Log::warning('Fonnte token is not set. WhatsApp message not sent.', [
                'target' => $phone,
                'message' => $message,
            ]);
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('Fonnte WhatsApp notification sent successfully.', [
                    'target' => $phone,
                    'response' => $response->json(),
                ]);
                return true;
            }

            Log::error('Fonnte WhatsApp notification failed.', [
                'target' => $phone,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('Exception occurred during Fonnte WhatsApp send.', [
                'target' => $phone,
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }
}
