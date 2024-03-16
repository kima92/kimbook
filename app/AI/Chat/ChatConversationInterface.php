<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 16/07/2023
 * Time: 0:50
 */

namespace App\AI\Chat;

use App\AI\Prompts\RawPrompt;
use Illuminate\Support\Collection;

interface ChatConversationInterface
{
    public function addSystemMessage(RawPrompt $prompt): static;

    public function send(RawPrompt $prompt): string;

    public function getUsages(): Collection;
    public function isSupportedLanguage(string $language): bool;
}
