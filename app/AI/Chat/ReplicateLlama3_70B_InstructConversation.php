<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 05/03/2024
 * Time: 11:21
 */

namespace App\AI\Chat;

use App\AI\Prompts\RawPrompt;

class ReplicateLlama3_70B_InstructConversation extends ReplicateLlama3Conversation
{
    protected string $currentModel = "meta-llama-3-70b-instruct";
    protected static array $costsPer1M = [
        "prompt" => 0.05,
        "completion" => 0.25,
    ];

    /**
     * @param RawPrompt $prompt
     *
     * @return string[]
     */
    protected function buildPayload(RawPrompt $prompt): array
    {
        return [
            "prompt"            => $prompt->__toString(),
            "top_k"             => 50,
            "top_p"             => 0.9,
            "max_tokens"        => 2000,
            "min_tokens"        => 0,
            "temperature"       => 0.6,
            "prompt_template"   => "<|begin_of_text|><|start_header_id|>system<|end_header_id|>\\n\\n{$this->systemMessage}\\n<|eot_id|><|start_header_id|>user<|end_header_id|>\\n\\n{prompt}<|eot_id|><|start_header_id|>assistant<|end_header_id|>\\n",
            "presence_penalty"  => 1.15,
            "frequency_penalty" => 0.2,
        ];
    }
}
