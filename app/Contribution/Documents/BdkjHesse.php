<?php

namespace App\Contribution\Documents;

use App\Contribution\Data\MemberData;
use App\Contribution\Traits\HasPdfBackground;
use App\Country;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BdkjHesse extends ContributionDocument
{

    use HasPdfBackground;

    /**
     * @param Collection<int, Collection<int, MemberData>> $members
     */
    public function __construct(
        public string $dateFrom,
        public string $dateUntil,
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

    /**
     * {@inheritdoc}
     */
    public static function fromRequest(array $request): self
    {
        return new self(
            dateFrom: $request['dateFrom'],
            dateUntil: $request['dateUntil'],
            zipLocation: $request['zipLocation'],
            country: Country::where('id', $request['country'])->firstOrFail(),
            members: MemberData::fromModels($request['members'])->chunk(20),
            eventName: $request['eventName'],
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function fromApiRequest(array $request): self
    {
        return new self(
            dateFrom: $request['dateFrom'],
            dateUntil: $request['dateUntil'],
            zipLocation: $request['zipLocation'],
            country: Country::where('id', $request['country'])->firstOrFail(),
            members: MemberData::fromApi($request['member_data'])->chunk(20),
            eventName: $request['eventName'],
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

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
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
            'eventName' => 'required|string',
        ];
    }
}
