<?php

namespace App\Contribution\Documents;

use App\Contribution\Contracts\HasContributionData;
use App\Contribution\Data\MemberData;
use App\Contribution\Traits\FormatsDates;
use App\Contribution\Traits\HasPdfBackground;
use App\Country;
use Illuminate\Support\Collection;

class WuppertalDocument extends ContributionDocument
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

    public static function fromPayload(HasContributionData $request): self
    {
        return new self(
            dateFrom: $request->dateFrom(),
            dateUntil: $request->dateUntil(),
            zipLocation: $request->zipLocation(),
            country: $request->country(),
            members: $request->members()->chunk(14),
            eventName: $request->eventName(),
        );
    }

    public static function getName(): string
    {
        return 'Wuppertal';
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
