<?php

namespace App\Contribution;

use App\Country;
use App\Member\Member;
use App\Pdf\EnvType;
use App\Pdf\PdfRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class DvData extends Data implements PdfRepository
{
    public function __construct(
        public string $dateFrom,
        public string $dateUntil,
        public string $zipLocation,
        public ?Country $country,
        public array $members,
        public ?string $filename = '',
        public $type = 'F',
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            dateFrom: $request->dateFrom,
            dateUntil: $request->dateUntil,
            zipLocation: $request->zipLocation,
            country: Country::findOrFail($request->country),
            members: $request->members,
        );
    }

    public function members(): Collection
    {
        return Member::whereIn('id', $this->members)->orderByRaw('lastname, firstname')->get();
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

    public function countryName(): string
    {
        return $this->country->name;
    }

    public function dateRange(): string
    {
        return Carbon::parse($this->dateFrom)->format('d.m.Y')
            .' - '
            .Carbon::parse($this->dateUntil)->format('d.m.Y');
    }

    public function getFilename(): string
    {
        return 'zuschuesse-dv';
    }

    public function getView(): string
    {
        return 'tex.zuschuss-dv';
    }

    public function getTemplate(): ?string
    {
        return 'zuschussdv';
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getScript(): EnvType
    {
        return EnvType::PDFLATEX;
    }
}
