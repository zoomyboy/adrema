<?php

namespace App\Contribution\Documents;

use App\Contribution\Data\MemberData;
use App\Contribution\Traits\FormatsDates;
use App\Contribution\Traits\HasPdfBackground;
use App\Country;
use App\Member\Member;
use Illuminate\Support\Collection;

class CityRemscheidDocument extends ContributionDocument
{
    use HasPdfBackground;
    use FormatsDates;

    /**
     * @param Collection<int, Collection<int, Member>> $leaders
     * @param Collection<int, Collection<int, Member>> $children
     */
    public function __construct(
        public string $dateFrom,
        public string $dateUntil,
        public string $zipLocation,
        public ?Country $country,
        public Collection $leaders,
        public Collection $children,
        public ?string $filename = '',
        public string $type = 'F',
        public string $eventName = '',
    ) {
        $this->setEventName($eventName);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromRequest(array $request): self
    {
        [$leaders, $children] = MemberData::fromModels($request['members'])->partition(fn ($member) => $member->isLeader);

        return new self(
            dateFrom: $request['dateFrom'],
            dateUntil: $request['dateUntil'],
            zipLocation: $request['zipLocation'],
            country: Country::where('id', $request['country'])->firstOrFail(),
            leaders: $leaders->values()->toBase()->chunk(6),
            children: $children->values()->toBase()->chunk(20),
            eventName: $request['eventName'],
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function fromApiRequest(array $request): self
    {
        $members = MemberData::fromApi($request['member_data']);
        [$leaders, $children] = $members->partition(fn ($member) => $member->isLeader);

        return new self(
            dateFrom: $request['dateFrom'],
            dateUntil: $request['dateUntil'],
            zipLocation: $request['zipLocation'],
            country: Country::where('id', $request['country'])->firstOrFail(),
            leaders: $leaders->values()->toBase()->chunk(6),
            children: $children->values()->toBase()->chunk(20),
            eventName: $request['eventName'],
        );
    }

    public static function getName(): string
    {
        return 'Remscheid';
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'dateFrom' => 'required|string|date_format:Y-m-d',
            'dateUntil' => 'required|string|date_format:Y-m-d',
            'zipLocation' => 'required|string',
            'country' => 'required|integer|exists:countries,id',
        ];
    }
}
