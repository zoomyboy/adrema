<?php

namespace App\Mailgateway\Types;

class LocalType extends Type
{
    public static function name(): string
    {
        return 'Lokal';
    }

    public function works(): bool
    {
        return true;
    }

    public static function fields(): array
    {
        return [];
    }

    public function setParams(array $params): static
    {
        return $this;
    }
}
