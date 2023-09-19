<?php

namespace Tests\Lib;

trait MergesAttributes
{
    /**
     * @return array<string, mixed>
     */
    abstract public function defaults(): array;

    /**
     * @param array<string, mixed> $overwrites
     * @return array<string, mixed>
     */
    public function attributes(?array $overwrites = []): array
    {
        $defaults = collect($this->defaults());

        foreach ($overwrites as $key => $value) {
            $defaults->put($key, $value);
        }

        return $defaults->toArray();
    }
}
