<?php
/**
 * Created by PhpStorm.
 * User: omer
 * Date: 26/06/2024
 * Time: 12:22
 */

namespace App\Utils;

use Session;

class LimitedFeature
{

    public function __construct(private string $name, private int $maxAttempts = 2) { }

    public static function forCreateBook(): static
    {
        return new LimitedFeature("create-book", 2);
    }

    public function getMax(): int
    {
        return $this->maxAttempts;
    }

    public function getCurrent(): string
    {
        return Session::get($this->getKey(), 0);
    }

    public function isAllowed()
    {
        return $this->maxAttempts > $this->getCurrent();
    }

    public function incr(): int
    {
        return Session::increment($this->getKey());
    }

    /**
     * @return string
     */
    protected function getKey(): string
    {
        return "guest_{$this->name}_count";
    }
}
