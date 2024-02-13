<?php

namespace App\Form\Enums;

enum NamiField: string
{
    case FIRSTNAME = 'Vorname';
    case BIRTHDAY = 'Geburtstag';

    /**
     * @return array<int, array{name: string, id: string}>
     */
    public static function forSelect(): array
    {
        return collect(static::cases())
            ->map(fn ($case) => ['id' => $case->value, 'name' => $case->value])
            ->toArray();
    }
}
