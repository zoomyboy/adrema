<?php

namespace App\Contribution\Documents;

use App\Contribution\Contracts\HasContributionData;
use App\Contribution\Traits\FormatsDates;
use App\Contribution\Traits\HasPdfBackground;
use App\Country;
use App\Member\Member;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CityRemscheidDocument extends ContributionDocument
{
    use HasPdfBackground;
    use FormatsDates;

    /**
     * @param Collection<int, Collection<int, Member>> $leaders
     * @param Collection<int, Collection<int, Member>> $children
     */
    public function __construct(
        public Carbon $dateFrom,
        public Carbon $dateUntil,
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

    public static function fromPayload(HasContributionData $request): self
    {
        [$leaders, $children] = $request->members()->partition(fn ($member) => $member->isLeader);

        return new self(
            dateFrom: $request->dateFrom(),
            dateUntil: $request->dateUntil(),
            zipLocation: $request->zipLocation(),
            country: $request->country(),
            leaders: $leaders->values()->toBase()->chunk(6),
            children: $children->values()->toBase()->chunk(20),
            eventName: $request->eventName(),
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
