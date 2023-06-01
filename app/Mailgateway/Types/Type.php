<?php

namespace App\Mailgateway\Types;

abstract class Type
{
    abstract public static function name(): string;

    abstract public static function fields(): array;

    abstract public function works(): bool;

    public static function defaults(): array
    {
        return collect(static::fields())->mapWithKeys(fn ($field) => [
            $field['name'] => $field['default'],
        ])->toArray();
    }
}
