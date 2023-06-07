<?php

namespace App\Mailgateway\Types;

abstract class Type
{
    abstract public static function name(): string;

    /**
     * @return array<int, MailgatewayCustomField>
     */
    abstract public static function fields(): array;

    abstract public function works(): bool;

    /**
     * @param array<string, mixed> $params
     */
    abstract public function setParams(array $params): static;

    public static function defaults(): array
    {
        return collect(static::fields())->mapWithKeys(fn ($field) => [
            $field['name'] => $field['default'],
        ])->toArray();
    }

    public static function presentFields(string $validator): array
    {
        return array_map(fn ($field) => [
            ...$field,
            'is_required' => str_contains($field[$validator], 'required'),
        ], static::fields());
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

    /**
     * @return array<string, string>
     */
    public static function fieldNames(): array
    {
        return collect(static::fields())->mapWithKeys(fn ($field) => [$field['name'] => $field['label']])->toArray();
    }
}
