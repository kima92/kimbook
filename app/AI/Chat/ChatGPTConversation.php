<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 15/07/2023
 * Time: 11:21
 */

namespace App\AI\Chat;

use App\AI\GenerateAIStatuses;
use App\AI\Prompts\RawPrompt;
use Illuminate\Support\Collection;
use OpenAI\Contracts\ClientContract;
use OpenAI\Responses\Chat\CreateResponse;

class ChatGPTConversation extends BaseConversation implements ChatConversationInterface
{

    const MODEL_GPT_4_O = 'gpt-4o';
    const MODEL_GPT_4_0125_PREVIEW = 'gpt-4-0125-preview';
    const MODEL_GPT_3_5_TURBO = 'gpt-3.5-turbo';

    protected string $currentModel = self::MODEL_GPT_4_O;
    protected static array $aiModels = [
        self::MODEL_GPT_4_O            => ["costsPer1K" => ["prompt" => 0.0050, "completion" => 0.0015]],
        self::MODEL_GPT_4_0125_PREVIEW => ["costsPer1K" => ["prompt" => 0.0100, "completion" => 0.0300]],
        self::MODEL_GPT_3_5_TURBO      => ["costsPer1K" => ["prompt" => 0.0005, "completion" => 0.0015]],
    ];

    protected array $supportedLanguages = ["en"];

    public function __construct(protected ClientContract $gptClient) { }

    public function addSystemMessage(RawPrompt $prompt): static
    {
        $this->messages = [
            ['role' => 'system', 'content' => $prompt->__toString()],
        ];

        return $this;
    }

    public function send(RawPrompt $prompt): GenerateMessageResult
    {
        $this->messages[] = ['role' => 'user', 'content' => $prompt->__toString()];

        $payload = [
            'model'           => $this->currentModel,
            'response_format' => ["type" => "json_object"],
            'messages'        => $this->messages,
        ];

        \Log::debug("[ChatGPTConversation] Requesting", $payload);

        /** @var CreateResponse $result */
        $result = retry(1, fn() => $this->gptClient->chat()->create($payload));

        \Log::debug("[ChatGPTConversation] Got response", $result->toArray());

        $this->usages[] = $result->usage->toArray();
        $message = $this->messages[] = $result['choices'][0]["message"];

        return new GenerateMessageResult($this->id, GenerateAIStatuses::Completed, $message["content"]);
    }

    public function getUsages(): Collection
    {
        $costs = self::$aiModels[$this->currentModel]['costsPer1K'];

        return collect($this->usages)->map(function (array $usage) use ($costs) {
            $usage['prompt_cost']     = ($costs['prompt'] * ($usage['prompt_tokens'] / 1000));
            $usage['completion_cost'] = ($costs['completion'] * ($usage['completion_tokens'] / 1000));

            return $usage;
        });
    }
}
