<?php

namespace App\Form\Enums;

use App\Member\Member;

enum NamiType: string
{
    case FIRSTNAME = 'Vorname';
    case LASTNAME = 'Nachname';
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

    public function getMemberAttribute(Member $member): ?string
    {
        return match ($this) {
            static::FIRSTNAME => $member->firstname,
            static::LASTNAME => $member->lastname,
            static::BIRTHDAY => $member->birthday?->format('Y-m-d'),
        };
    }
}
