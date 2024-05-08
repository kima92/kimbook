<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 05/03/2024
 * Time: 11:21
 */

namespace App\AI\Chat;

use App\AI\GenerateAIStatuses;
use App\AI\Prompts\RawPrompt;
use BenBjurstrom\Replicate\Replicate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ReplicateLlama3Conversation extends BaseConversation implements ChatConversationInterface
{
    protected ?string $systemMessage = null;
    protected string $currentModel = "meta-llama-3-8b";

    protected static array $costsPer1M = [
        "prompt" => 0.05,
        "completion" => 0.25,
    ];
    protected array $supportedLanguages = ["en", "he"];

    public function __construct(
        protected Replicate $client,
        protected array $messages = [],
        array $usages = [],
    ) {
        $this->usages = $usages;
    }

    public function addSystemMessage(RawPrompt $prompt): static
    {
        $this->systemMessage = $prompt->__toString();

        return $this;
    }

    // Not real Conversation/assistant implemented. just prompt with system message
    public function send(RawPrompt $prompt): GenerateMessageResult
    {
        $payload = [
            "input" => $this->buildPayload($prompt),
            "webhook" => config("app.url") . "/api/replicate/llama-3/{$this->id}?model={$this->currentModel}",
            "webhook_events_filter" => ["completed"],
            "stream" => false,
        ];
        $url = "https://api.replicate.com/v1/models/meta/{$this->currentModel}/predictions";

        \Log::debug("[ReplicateLlama3Conversation][send] Requesting Llama3", $payload + [ "url" => $url]);

        /** @var \Illuminate\Http\Client\Response $result */
        $result = retry(1, fn() => Http::asJson()
            ->withToken(config("services.replicate.api_key"))
            ->post($url, $payload)->throw());

        \Log::debug("[ReplicateLlama3Conversation][send] Got response from Llama3", $result->json());

        return new GenerateMessageResult($this->id, GenerateAIStatuses::Initial, null);
    }

    public function getUsages(): Collection
    {
        return collect($this->usages)->map(function (array $usage) {
            $usage['prompt_cost']     = (self::$costsPer1M['prompt']     * ($usage['input_tokens']  / 1000000));
            $usage['completion_cost'] = (self::$costsPer1M['completion'] * ($usage['output_tokens'] / 1000000));

            return $usage;
        });
    }

    /**
     * @param RawPrompt $prompt
     *
     * @return string[]
     */
    protected function buildPayload(RawPrompt $prompt): array
    {
        return [
            "prompt" => $this->systemMessage . $prompt->__toString(),
            "top_k" => 0,
            "top_p" => 0.9,
            "temperature" => 0.6,
            "length_penalty" => 1,
            "max_new_tokens" => 650,
            "prompt_template" => "{prompt}",
            "presence_penalty" => 1.15,
        ];
    }
}
