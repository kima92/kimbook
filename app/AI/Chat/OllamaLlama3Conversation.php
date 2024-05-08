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
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use OpenAI\Contracts\ClientContract;

class OllamaLlama3Conversation extends BaseConversation implements ChatConversationInterface
{
    protected string $systemMessages = "";

    protected array $supportedLanguages = ["en"];

    public function __construct(protected ClientContract $gptClient) { }

    public function addSystemMessage(RawPrompt $prompt): static
    {
        $this->systemMessages = $prompt->__toString();

        return $this;
    }

    public function send(RawPrompt $prompt): GenerateMessageResult
    {
        $url = "http://localhost:11434/api/generate";
        $payload = [
            "model"  => "llama3",
            "prompt" => $this->systemMessages . " " . $prompt->__toString(),
            "format" => "json",
            "stream" => false
        ];
        \Log::debug("[OllamaLlama3Conversation][send] Requesting", $payload + [ "url" => $url]);

        /** @var \Illuminate\Http\Client\Response $result */
        $result = retry(1, fn() => Http::asJson()->post($url, $payload));

        \Log::debug("[OllamaLlama3Conversation][send] Response", Arr::except($result->json(), ["context"]));

        return new GenerateMessageResult($this->id, GenerateAIStatuses::Completed, $result["response"]);
    }

    public function getUsages(): Collection
    {
        return collect([]);
    }

    public static function aaa()
    {
        return Http::asJson()->post("http://localhost:11434/api/generate", [
            "model"  => "llama3",
            "prompt" => "You are an author that writes books series for small children (age 3-11). user
 will give guidelines such as names of characters, relationships between them, their hobbies and maybe also the environment in which they live. If user don't give enough information you have to complete
it yourself.
The stories MUST be optimistic and positive, interesting, educational, improve the child's courage and self-confidence. It is very important to include morals and educational messages suitable for childr
en in every book such as family respect, helping others, being a good friend and more. SPECIFICALLY helping-others
Story language should be in English regardless user input. each chapter have 1-3 short sentences. no more than 5-7 chapters.
Illustrator description must be consistent and detailed. Every image description is sent as is without context to different illustrator so you must instruct them about the character appearance, gender, a
rt and drawing style. always ask for Israeli looking unless specified other.
illustrator_instructions_prompt is always English, 2-3 sentences, describing the scene, including the environment, characters, what they are doing, emotions, view, and camera angle. MUST NEVER DESCRIBE B
Y NAMES, ONLY BY APPEARANCE! Declare genders on each image.
General art is inspired by Walt Disney

You reply in JSON format with the fields 'title','description','art','tags','chapters'. each chapter is an object containing the fields 'title','content','illustrator_instructions_prompt'.
No need to answer anything but the JSON object.

Now here is the input: אלכס ילד בן 4 פוגש טיגריס סיבירי",
            "format" => "json",
            "stream" => false
        ]);
    }
}
