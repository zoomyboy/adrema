<?php

namespace App\Pdf\Data;

use App\Member\Member;
use App\Pdf\EnvType;
use App\Pdf\PdfRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

class MemberEfzData extends Data implements PdfRepository
{
    public function __construct(
        public ?string $name,
        public ?string $secondLine,
        public ?string $currentDate,
        public ?array $sender = [],
        public ?string $filename = '',
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $memberId = $request->member;

        $member = Member::findOrFail($memberId);

        return new self(
            name: $member->fullname,
            secondLine: "geb. am {$member->birthday->format('d.m.Y')}, wohnhaft in {$member->location}",
            currentDate: now()->format('d.m.Y'),
            sender: [
                $member->fullname,
                $member->address,
                $member->zip.' '.$member->location,
                'Mglnr.: '.$member->nami_id,
            ]
        );
    }

    public function getFilename(): string
    {
        return 'efz-fuer-'.Str::slug($this->name);
    }

    public function getView(): string
    {
        return 'tex.efz';
    }

    public function getTemplate(): string
    {
        return 'efz';
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
