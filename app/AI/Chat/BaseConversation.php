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

    protected ?string $id = null;
    protected string $currentModel = '';
    protected static array $aiModels = [];

    protected array $usages = [];
    protected array $messages = [];
    protected array $supportedLanguages = [];

    public function getModel(): string
    {
        return $this->currentModel;
    }

    public function isSupportedLanguage(string $language): bool
    {
        return in_array($language, $this->supportedLanguages);
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }
}
