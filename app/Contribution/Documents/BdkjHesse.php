<?php

namespace App\Contribution\Documents;

use App\Contribution\Contracts\HasContributionData;
use App\Contribution\Data\MemberData;
use App\Contribution\Traits\HasPdfBackground;
use App\Country;
use App\Form\Enums\SpecialType;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BdkjHesse extends ContributionDocument
{

    use HasPdfBackground;

    /**
     * @param Collection<int, Collection<int, MemberData>> $members
     */
    public function __construct(
        public Carbon $dateFrom,
        public Carbon $dateUntil,
        public string $zipLocation,
        public ?Country $country,
        public Collection $members,
        public string $eventName,
        public ?string $filename = '',
        public string $type = 'F',
    ) {
        $this->setEventName($eventName);
    }

    public function dateFrom(): string
    {
        return Carbon::parse($this->dateFrom)->format('d.m.Y');
    }

    public function dateUntil(): string
    {
        return Carbon::parse($this->dateUntil)->format('d.m.Y');
    }

    public static function fromPayload(HasContributionData $request): self
    {
        return new self(
            dateFrom: $request->dateFrom(),
            dateUntil: $request->dateUntil(),
            zipLocation: $request->zipLocation(),
            country: $request->country(),
            members: $request->members()->chunk(20),
            eventName: $request->eventName(),
        );
    }

    public function countryName(): string
    {
        return $this->country->name;
    }

    public function durationDays(): int
    {
        return intVal(Carbon::parse($this->dateUntil)->diffInDays(Carbon::parse($this->dateFrom))) + 1;
    }

    /**
     * @param Collection<int, MemberData> $chunk
     */
    public function membersDays(Collection $chunk): int
    {
        return $this->durationDays() * $chunk->count();
    }

    public function pages(): int
    {
        return count($this->members);
    }

    public function memberName(MemberData $member): string
    {
        return $member->separatedName();
    }

    public function memberCity(MemberData $member): string
    {
        return $member->city();
    }

    public function memberGender(MemberData $member): string
    {
        if (!$member->gender) {
            return '';
        }

        return strtolower(substr($member->gender->name, 0, 1));
    }

    public function memberBirthYear(MemberData $member): string
    {
        return $member->birthYear();
    }

    public static function getName(): string
    {
        return 'BDKJ Hessen';
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'dateFrom' => 'required|string|date_format:Y-m-d',
            'dateUntil' => 'required|string|date_format:Y-m-d',
            'country' => 'required|integer|exists:countries,id',
            'zipLocation' => 'required|string',
        ];
    }

    public static function requiredFormSpecialTypes(): array {
        return [
            SpecialType::FIRSTNAME,
            SpecialType::LASTNAME,
            SpecialType::BIRTHDAY,
            SpecialType::ZIP,
            SpecialType::LOCATION,
            SpecialType::GENDER,
        ];
    }
}
