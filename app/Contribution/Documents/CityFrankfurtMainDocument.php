<?php

namespace App\Contribution\Documents;

use App\Contribution\Data\MemberData;
use App\Country;
use App\Invoice\InvoiceSettings;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Zoomyboy\Tex\Engine;
use Zoomyboy\Tex\Template;

class CityFrankfurtMainDocument extends ContributionDocument
{
    public string $fromName;

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
        $this->fromName = app(InvoiceSettings::class)->from_long;
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
            members: MemberData::fromModels($request['members'])->chunk(15),
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
            members: MemberData::fromApi($request['member_data'])->chunk(15),
            eventName: $request['eventName'],
        );
    }

    public function dateFromHuman(): string
    {
        return Carbon::parse($this->dateFrom)->format('d.m.Y');
    }

    public function dateUntilHuman(): string
    {
        return Carbon::parse($this->dateUntil)->format('d.m.Y');
    }


    public function countryName(): string
    {
        return $this->country->name;
    }

    public function pages(): int
    {
        return count($this->members);
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

    public function memberAge(MemberData $member): string
    {
        return $member->age();
    }

    public function basename(): string
    {
        return 'zuschuesse-frankfurt-' . Str::slug($this->eventName);
    }

    public function view(): string
    {
        return 'tex.contribution.city-frankfurt-main';
    }

    public function template(): Template
    {
        return Template::make('tex.templates.contribution');
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getEngine(): Engine
    {
        return Engine::PDFLATEX;
    }

    public static function getName(): string
    {
        return 'Für Frankfurt erstellen';
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
