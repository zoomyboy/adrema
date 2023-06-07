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

    public static function rules(string $validator): array
    {
        return collect(static::fields())->mapWithKeys(fn ($field) => [
            $field['name'] => $field[$validator],
        ])->toArray();
    }

    public function toResource(): array
    {
        return [
            'cls' => get_class($this),
            'params' => get_object_vars($this),
        ];
    }
}
