<?php

namespace App\Form\Enums;

use App\Form\Models\Form;
use App\Group\Enums\Level;
use App\Member\Member;
use Illuminate\Database\Eloquent\Builder;

enum NamiType: string
{
    case FIRSTNAME = 'Vorname';
    case LASTNAME = 'Nachname';
    case NICKNAME = 'Spitzname';
    case BIRTHDAY = 'Geburtstag';
    case REGION = 'Bezirk';
    case STAMM = 'Stamm';
    case EMAIL = 'E-Mail-Adresse';
    case ADDRESS = 'Adresse';
    case ZIP = 'PLZ';
    case LOCATION = 'Ort';
    case GENDER = 'Geschlecht';
    case MOBILEPHONE = 'Handynummer';
    case AGE = 'Alter (zum Zeitpunkt der Anmeldung)';
    case AGEEVENT = 'Alter (zum Zeitpunkt der Veranstaltung)';

    /**
     * @return array<int, array{name: string, id: string}>
     */
    public static function forSelect(): array
    {
        return collect(static::cases())
            ->map(fn ($case) => ['id' => $case->value, 'name' => $case->value])
            ->toArray();
    }

    public function getMemberAttribute(Member $member, Form $form): ?string
    {
        return match ($this) {
            static::FIRSTNAME => $member->firstname,
            static::LASTNAME => $member->lastname,
            static::BIRTHDAY => $member->birthday?->format('Y-m-d'),
            static::REGION => $this->matchRegion($member),
            static::STAMM => $this->matchGroup($member),
            static::EMAIL => $member->email,
            static::ADDRESS => $member->address,
            static::ZIP => $member->zip,
            static::LOCATION => $member->location,
            static::NICKNAME => $member->nickname,
            static::GENDER => $member->gender?->name,
            static::AGE => intVal($member->birthday->diffInYears(now())),
            static::AGEEVENT => $member->birthday->diffInYears($form->from),
            static::MOBILEPHONE => $member->mobile_phone,
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
            default => $query,
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
