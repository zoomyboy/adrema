<?php

namespace App\Contribution\Documents;

use App\Contribution\Data\MemberData;
use App\Country;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Zoomyboy\Tex\Engine;
use Zoomyboy\Tex\Template;

class GallierDocument extends ContributionDocument
{
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
    ) {
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
            members: MemberData::fromModels($request['members'])->chunk(14),
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

    public function basename(): string
    {
        return 'zuschuesse-gallier';
    }

    public function view(): string
    {
        return 'tex.contribution.gallier';
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
        return 'Für RdP NRW erstellen';
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
