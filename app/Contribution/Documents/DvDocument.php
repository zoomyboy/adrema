<?php

namespace App\Contribution\Documents;

use App\Country;
use App\Member\Member;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Zoomyboy\Tex\Engine;
use Zoomyboy\Tex\Template;

class DvDocument extends ContributionDocument
{
    /**
     * @param Collection<int, Collection<int, Member>> $members
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

    /**
     * @param array<string, mixed> $payload
     */
    abstract public static function fromRequest(array $payload): self;

    public function dateRange(): string
    {
        return Carbon::parse($this->dateFrom)->format('d.m.Y')
            .' - '
            .Carbon::parse($this->dateUntil)->format('d.m.Y');
    }

    /**
     * @param array<string, string|int> $request
     */
    public static function fromRequest(array $request): self
    {
        return new self(
            dateFrom: $request['dateFrom'],
            dateUntil: $request['dateUntil'],
            zipLocation: $request['zipLocation'],
            country: Country::where('id', $request['country'])->firstOrFail(),
            members: Member::whereIn('id', $request['members'])->orderByRaw('lastname, firstname')->get()->toBase()->chunk(17),
        );
    }

    public function countryName(): string
    {
        return $this->country->name;
    }

    public function memberShort(Member $member): string
    {
        return $member->isLeader() ? 'L' : '';
    }

    public function memberName(Member $member): string
    {
        return $member->lastname.', '.$member->firstname;
    }

    public function memberAddress(Member $member): string
    {
        return $member->fullAddress;
    }

    public function memberGender(Member $member): string
    {
        if (!$member->gender) {
            return '';
        }

        return strtolower(substr($member->gender->name, 0, 1));
    }

    public function memberAge(Member $member): string
    {
        return (string) $member->getAge();
    }

    public function basename(): string
    {
        return 'zuschuesse-dv';
    }

    public function view(): string
    {
        return 'tex.zuschuss-dv';
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
        return 'Für DV erstellen';
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
