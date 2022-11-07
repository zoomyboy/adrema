<?php

namespace App\Contribution;

use App\Member\Member;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Zoomyboy\Tex\Document;
use Zoomyboy\Tex\Engine;
use Zoomyboy\Tex\Template;

class SolingenDocument extends Document
{
    final private function __construct(
        public string $dateFrom,
        public string $dateUntil,
        public string $zipLocation,
        /** @var array<int, int> */
        public array $members,
        public string $eventName,
        public string $type = 'F',
    ) {
    }

    public static function fromRequest(Request $request): static
    {
        return new static(
            dateFrom: $request->dateFrom,
            dateUntil: $request->dateUntil,
            zipLocation: $request->zipLocation,
            members: $request->members,
            eventName: $request->eventName,
        );
    }

    /**
     * @return Collection<Collection<Member>>
     */
    public function memberModels(): Collection
    {
        return Member::whereIn('id', $this->members)->orderByRaw('lastname, firstname')->get()->chunk(14);
    }

    public function niceEventFrom(): string
    {
        return Carbon::parse($this->dateFrom)->format('d.m.Y');
    }

    public function niceEventUntil(): string
    {
        return Carbon::parse($this->dateUntil)->format('d.m.Y');
    }

    public function template(): Template
    {
        return Template::make('tex.templates.contribution');
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

    public function basename(): string
    {
        return 'zuschuesse-solingen-'.Str::slug($this->eventName);
    }

    public function view(): string
    {
        return 'tex.zuschuss-stadt';
    }

    public function getEngine(): Engine
    {
        return Engine::PDFLATEX;
    }
}
