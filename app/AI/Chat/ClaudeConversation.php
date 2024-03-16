<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 05/03/2024
 * Time: 11:21
 */

namespace App\AI\Chat;

use App\AI\Claude\Client;
use App\AI\Prompts\RawPrompt;
use Illuminate\Support\Collection;

class ClaudeConversation extends BaseConversation implements ChatConversationInterface
{
    protected ?string $systemMessage = null;
    protected string $currentModel = Client::MODEL_SONNET;
    protected static array $aiModels = [
        Client::MODEL_OPUS   => ["costsPer1M" => ["prompt" => 15, "completion" => 70]],
        Client::MODEL_SONNET => ["costsPer1M" => ["prompt" => 3,  "completion" => 15]],
    ];
    protected array $supportedLanguages = ["en", "he"];

    public function __construct(
        protected Client $claudeClient,
        protected array $messages = [],
    ) { }

    public function addSystemMessage(RawPrompt $prompt): static
    {
        $this->systemMessage = $prompt->__toString();

        return $this;
    }

    public function send(RawPrompt $prompt): string
    {
        $this->messages[] = ['role' => 'user', 'content' => $prompt->__toString()];

        $result = retry(1, fn() => $this->claudeClient->send($this->messages, $this->currentModel, $this->systemMessage));

        \Log::debug("[ClaudeConversation][send] Got response from Claude", $result);
        $this->usages[] = $result['usage'];
        $message = $this->messages[] = ['role' => 'assistant', 'content' => $result['content'][0]['text']];

        return $message['content'];
    }

    public function getUsages(): Collection
    {
        $costs = self::$aiModels[$this->currentModel]['costsPer1M'];

        return collect($this->usages)->map(function (array $usage) use ($costs) {
            $usage['prompt_cost']     = ($costs['prompt'] * ($usage['input_tokens'] / 1000000));
            $usage['completion_cost'] = ($costs['completion'] * ($usage['output_tokens'] / 1000000));

            return $usage;
        });
    }
}
