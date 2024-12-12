<?php

namespace App\Lib;

use Spatie\LaravelData\Data;

class Sorting extends Data
{
    public static function by(string $by): self
    {
        return static::factory()->withoutMagicalCreation()->from(['by' => $by]);
    }

    public function __construct(public string $by, public bool $direction = false)
    {
    }

    /**
     * @return array<int, string>
     */
    public function toMeilisearch(): array
    {
        return [$this->by . ':' . ($this->direction ? 'desc' : 'asc')];
    }
}
