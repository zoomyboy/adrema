<?php

namespace App\Letter;

enum BillKind: string
{
    case EMAIL = 'E-Mail';
    case POST = 'Post';
    /**
     * @return array<string, string>
     */
    public static function values(): array
    {
        return collect(static::cases())->map(fn ($case) => $case->value)->toArray();
    }

    /**
     * @return array<int, array{name: string, id: string}>
     */
    public static function forSelect(): array
    {
        return collect(static::cases())
            ->map(fn ($case) => ['id' => $case->value, 'name' => $case->value])
            ->toArray();
    }

    public static function fromValue(string $value): self
    {
        return collect(static::cases())->firstOrFail(fn ($case) => $case->value === $value);
    }
}
