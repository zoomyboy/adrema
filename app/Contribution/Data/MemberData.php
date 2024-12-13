<?php

namespace App\Contribution\Data;

use App\Gender;
use App\Member\Member;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class MemberData extends Data
{
    public function __construct(
        public string $firstname,
        public string $lastname,
        public string $address,
        public string $zip,
        public string $location,
        public Carbon $birthday,
        public ?Gender $gender,
        public bool $isLeader
    ) {
    }

    /**
     * @param array<int, int> $ids
     *
     * @return Collection<int, static>
     */
    public static function fromModels(array $ids): Collection
    {
        return Member::whereIn('id', $ids)->orderByRaw('lastname, firstname')->get()->map(fn ($member) => self::factory()->withoutMagicalCreation()->from([
            ...$member->toArray(),
            'birthday' => $member->birthday->toAtomString(),
            'isLeader' => $member->isLeader(),
            'gender' => $member->gender,
        ]))->toBase();
    }

    /**
     * @param array<int, ContributionMemberData> $data
     *
     * @return Collection<int, static>
     */
    public static function fromApi(array $data): Collection
    {
        return collect($data)->map(fn ($member) => self::factory()->withoutMagicalCreation()->from([
            ...$member,
            'birthday' => Carbon::parse($member['birthday'])->toAtomString(),
            'gender' => Gender::fromString($member['gender']),
            'isLeader' => $member['is_leader'],
        ]));
    }

    public function fullname(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function separatedName(): string
    {
        return $this->lastname . ', ' . $this->firstname;
    }

    public function fullAddress(): string
    {
        return $this->address . ', ' . $this->zip . ' ' . $this->location;
    }

    public function city(): string
    {
        return $this->zip . ' ' . $this->location;
    }

    public function age(): string
    {
        return (string) $this->birthday->diffInYears(now()) ?: '';
    }

    public function birthYear(): string
    {
        return (string) $this->birthday->year;
    }

    public function birthdayHuman(): string
    {
        return $this->birthday->format('d.m.Y');
    }

    public function genderLetter(): string
    {
        return $this->gender->short;
    }
}
