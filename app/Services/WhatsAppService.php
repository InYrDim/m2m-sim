<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public static function send(string $phone, string $message)
    {
        $token = config('services.fonnte.token');
        $url = config('services.fonnte.url');

        $response = Http::withHeaders([
                'Authorization' => $token,
            ])
            ->asMultipart()
            ->post($url, [
                [
                    'name' => 'target',
                    'contents' => $phone,
                ],
                [
                    'name' => 'message',
                    'contents' => $message,
                ],
                [
                    'name' => 'countryCode',
                    'contents' => '62',
                ],
            ]);

        return $response->json();
    }
}
