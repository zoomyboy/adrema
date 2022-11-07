<?php

namespace App\Contribution;

use App\Country;
use App\Member\Member;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Zoomyboy\Tex\Document;
use Zoomyboy\Tex\Engine;
use Zoomyboy\Tex\Template;

class DvDocument extends Document
{
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
            .' - '
            .Carbon::parse($this->dateUntil)->format('d.m.Y');
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            dateFrom: $request->dateFrom,
            dateUntil: $request->dateUntil,
            zipLocation: $request->zipLocation,
            country: Country::findOrFail($request->country),
            members: Member::whereIn('id', $request->members)->orderByRaw('lastname, firstname')->get()->chunk(17),
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
}
