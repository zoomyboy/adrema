<?php

namespace App\Contribution\Documents;

use App\Contribution\Data\MemberData;
use App\Country;
use App\Member\Member;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Zoomyboy\Tex\Engine;
use Zoomyboy\Tex\Template;

class CityRemscheidDocument extends ContributionDocument
{
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
    ) {
    }

    public function niceDateFrom(): string
    {
        return Carbon::parse($this->dateFrom)->format('d.m.Y');
    }

    public function niceDateUntil(): string
    {
        return Carbon::parse($this->dateUntil)->format('d.m.Y');
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
        );
    }

    public function basename(): string
    {
        return 'zuschuesse-remscheid';
    }

    public function view(): string
    {
        return 'tex.contribution.city-remscheid';
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
        return 'Für Remscheid erstellen';
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
