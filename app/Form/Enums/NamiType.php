<?php

namespace App\Form\Enums;

use App\Group\Enums\Level;
use App\Member\Member;
use Illuminate\Database\Eloquent\Builder;

enum NamiType: string
{
    case FIRSTNAME = 'Vorname';
    case LASTNAME = 'Nachname';
    case BIRTHDAY = 'Geburtstag';
    case REGION = 'Bezirk';
    case STAMM = 'Stamm';
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

    public function getMemberAttribute(Member $member): ?string
    {
        return match ($this) {
            static::FIRSTNAME => $member->firstname,
            static::LASTNAME => $member->lastname,
            static::BIRTHDAY => $member->birthday?->format('Y-m-d'),
            static::REGION => $this->matchRegion($member),
            static::STAMM => $this->matchGroup($member),
            static::EMAIL => $member->email,
        };
    }

    /**
     * @param Builder<Member> $query
     * @return Builder<Member>
     */
    public function performQuery(Builder $query, mixed $value): Builder
    {
        return match ($this) {
            static::FIRSTNAME => $query->where('firstname', $value),
            static::LASTNAME => $query->where('lastname', $value),
            static::BIRTHDAY => $query->where('birthday', $value),
            static::REGION => $query,
            static::STAMM => $query,
            static::EMAIL => $query->where('email', $value),
        };
    }

    protected function matchGroup(Member $member): ?int
    {
        if ($member->group->level === Level::GROUP) {
            return $member->group_id;
        }

        return null;
    }

    protected function matchRegion(Member $member): ?int
    {
        if ($member->group->parent?->level === Level::REGION) {
            return $member->group->parent->id;
        }

        if ($member->group->level === Level::REGION) {
            return $member->group_id;
        }

        return null;
    }
}
