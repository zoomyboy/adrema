<?php

namespace App\Form\Enums;

enum SpecialType: string
{
    case FIRSTNAME = 'Vorname';
    case LASTNAME = 'Nachname';
    case EMAIL = 'E-Mail-Adresse';

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
