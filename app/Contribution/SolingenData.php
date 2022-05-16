<?php

namespace App\Contribution;

use App\Member\Member;
use App\Pdf\EnvType;
use App\Pdf\PdfRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

class SolingenData extends Data implements PdfRepository
{
    public function __construct(
        public string $eventName,
        public string $dateFrom,
        public string $dateUntil,
        public ?string $filename = '',
        public $type = 'FK',
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            eventName: $request->eventName,
            dateFrom: $request->dateFrom,
            dateUntil: $request->dateUntil,
        );
    }

    public function checkboxes(): string
    {
        $output = '';

        $firstRow = collect(['B' => 'Jugendbildungsmaßnahme', 'G' => 'Gruppenleiter/innenschulung', 'FK' => 'Ferienkolonie', 'F' => 'Freizeitnaßnahme'])->map(function ($item, $key) {
            return ($this->type === $key ? '\\checkedcheckbox' : '\\checkbox').'{'.$item.'}';
        })->implode(' & ').' \\\\';

        $secondRow = collect(['I' => 'Int. Jugendbegegnung', 'P' => 'politische Jugendbildung', 'PR' => 'Projekte'])->map(function ($item, $key) {
            return ($this->type === $key ? '\\checkedcheckbox' : '\\checkbox').'{'.$item.'}';
        })->implode(' & ').' & \\emptycheckbox \\\\';

        return $firstRow."\n".$secondRow;
    }

    public function members(): Collection
    {
        return Member::orderByRaw('lastname, firstname')->get();
    }

    public function niceEventFrom(): string
    {
        return Carbon::parse($this->dateFrom)->format('d.m.Y');
    }

    public function niceEventTo(): string
    {
        return Carbon::parse($this->dateUntil)->format('d.m.Y');
    }

    public function getFilename(): string
    {
        return 'zuschuesse-solingen-'.Str::slug($this->eventName);
    }

    public function getView(): string
    {
        return 'tex.zuschuss-stadt';
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
