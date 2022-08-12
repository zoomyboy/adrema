<?php

namespace Tests\Lib;

trait MergesAttributes
{
    abstract public function defaults();

    public function attributes(?array $overwrites = []): array
    {
        $defaults = collect($this->defaults());

        foreach ($overwrites as $key => $value) {
            $defaults->put($key, $value);
        }

        return $defaults->toArray();
    }
}
