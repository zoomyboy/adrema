<?php

namespace App\Contribution\Documents;

use App\Contribution\Data\MemberData;
use App\Contribution\Traits\HasPdfBackground;
use App\Country;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RdpNrwDocument extends ContributionDocument
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
        public ?string $filename = '',
        public string $type = 'F',
        public string $eventName = '',
    ) {
        $this->setEventName($eventName);
    }

    public function dateRange(): string
    {
        return Carbon::parse($this->dateFrom)->format('d.m.Y')
            . ' - '
            . Carbon::parse($this->dateUntil)->format('d.m.Y');
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
            members: MemberData::fromModels($request['members'])->chunk(17),
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
            members: MemberData::fromApi($request['member_data'])->chunk(17),
            eventName: $request['eventName'],
        );
    }

    public function countryName(): string
    {
        return $this->country->name;
    }

    public function memberShort(MemberData $member): string
    {
        return $member->isLeader ? 'L' : '';
    }

    public function memberName(MemberData $member): string
    {
        return $member->separatedName();
    }

    public function memberAddress(MemberData $member): string
    {
        return $member->fullAddress();
    }

    public function memberGender(MemberData $member): string
    {
        if (!$member->gender) {
            return '';
        }

        return strtolower(substr($member->gender->name, 0, 1));
    }

    public function memberAge(MemberData $member): string
    {
        return $member->age();
    }

    public static function getName(): string
    {
        return 'RdP NRW';
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
}
