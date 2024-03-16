<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 15/07/2023
 * Time: 11:21
 */

namespace App\AI\Chat;

use App\AI\Prompts\RawPrompt;
use Illuminate\Support\Collection;
use OpenAI\Contracts\ClientContract;

class ChatGPTConversation extends BaseConversation implements ChatConversationInterface
{

    const MODEL_GPT_4_0125_PREVIEW = 'gpt-4-0125-preview';
    const MODEL_GPT_3_5_TURBO = 'gpt-3.5-turbo';

    protected string $currentModel = self::MODEL_GPT_4_0125_PREVIEW;
    protected static array $aiModels = [
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

    public function send(RawPrompt $prompt): string
    {
        $this->messages[] = ['role' => 'user', 'content' => $prompt->__toString()];

//        var_dump($this->messages);

        $result = retry(1,
        fn() => $this->gptClient->chat()->create([
            'model'    => $this->currentModel,
//            'model' => 'gpt-4',
            'messages' => $this->messages,
        ]));

        $this->usages[] = $result->usage->toArray();
        $message = $this->messages[] = $result['choices'][0]["message"];

        return $message["content"];
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
