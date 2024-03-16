<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 15/07/2024
 * Time: 11:21
 */

namespace App\AI\Chat;

abstract class BaseConversation implements ChatConversationInterface
{

    protected string $currentModel = '';
    protected static array $aiModels = [];

    protected array $usages = [];
    protected array $messages = [];
    protected array $supportedLanguages = [];

    /**
     * @param $usage
     *
     * @return float[]
     */
    protected function enrichCosts($usage): array
    {
        $costs = static::$aiModels[$this->currentModel]['costsPer1K'];

        $usage['prompt_cost']     = ($costs['prompt'] * ($usage['prompt_tokens'] / 1000));
        $usage['completion_cost'] = ($costs['completion'] * ($usage['completion_tokens'] / 1000));

        return $usage;
    }

    public function getModel(): string
    {
        return $this->currentModel;
    }

    public function isSupportedLanguage(string $language): bool
    {
        return in_array($language, $this->supportedLanguages);
    }

}
