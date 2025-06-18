<?php

namespace App\Contribution\Documents;

use App\Contribution\Contracts\HasContributionData;
use App\Contribution\Data\MemberData;
use App\Contribution\Traits\FormatsDates;
use App\Contribution\Traits\HasPdfBackground;
use App\Country;
use App\Invoice\InvoiceSettings;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CityFrankfurtMainDocument extends ContributionDocument
{
    use HasPdfBackground;
    use FormatsDates;

    public string $fromName;

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
        $this->fromName = app(InvoiceSettings::class)->from_long;
    }

    public static function fromPayload(HasContributionData $request): self
    {
        return new self(
            dateFrom: $request->dateFrom(),
            dateUntil: $request->dateUntil(),
            zipLocation: $request->zipLocation(),
            country: $request->country(),
            members: $request->members()->chunk(15),
            eventName: $request->eventName(),
        );
    }

    public function countryName(): string
    {
        return $this->country->name;
    }

    public function pages(): int
    {
        return count($this->members);
    }

    public static function getName(): string
    {
        return 'Frankfurt';
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
