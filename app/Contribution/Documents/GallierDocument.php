<?php

namespace App\Contribution\Documents;

use App\Contribution\Data\MemberData;
use App\Contribution\Traits\FormatsDates;
use App\Contribution\Traits\HasPdfBackground;
use App\Country;
use Illuminate\Support\Collection;

class GallierDocument extends ContributionDocument
{

    use HasPdfBackground;
    use FormatsDates;

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
            members: MemberData::fromModels($request['members'])->chunk(14),
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
            members: MemberData::fromApi($request['member_data'])->chunk(14),
            eventName: $request['eventName'],
        );
    }

    public static function getName(): string
    {
        return 'Gallier';
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
        ];
    }
}
