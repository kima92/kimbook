<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 04/03/2024
 * Time: 22:45
 */

namespace App\AI\Claude;

use Illuminate\Support\Facades\Http;

class Client
{
    const MODEL_OPUS = 'claude-3-opus-20240229';
    const MODEL_SONNET = 'claude-3-sonnet-20240229';

    public function __construct(protected string $apiKey) {
    }

    public function send(array $messages, string $model = "claude-3-opus-20240229", ?string $systemMessage = null, int $maxTokens = 2024): array
    {
        return Http::withHeaders([
            "x-api-key" => $this->apiKey,
            "anthropic-version" => "2023-06-01"
        ])
            ->asJson()
            ->acceptJson()
            ->post("https://api.anthropic.com/v1/messages", array_filter([
                "model"      => $model,
                "max_tokens" => $maxTokens,
                "messages"   => $messages,
                "system"     => $systemMessage,
            ]))->json();
    }
}
