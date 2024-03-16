<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 15/07/2023
 * Time: 11:18
 */

namespace App\AI\Prompts;

class RawPrompt implements \Stringable
{
    protected array $messages = [];

    public function __construct(protected string $base) { }

    public function __toString(): string
    {
        return $this->base;
    }
}
