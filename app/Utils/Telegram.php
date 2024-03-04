<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 01/03/2024
 * Time: 18:58
 */

namespace App\Utils;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Telegram
{

    public function send(string $message, ?int $chatId = null): ?array
    {
        $chatId ??= config('services.telegram.default_chat_id');
        $apiKey = config('services.telegram.api_key');
        if (!$chatId || !$apiKey) {
            return null;
        }

        $response = Http::get("https://api.telegram.org/bot{$apiKey}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        return $response->json();
    }
}
