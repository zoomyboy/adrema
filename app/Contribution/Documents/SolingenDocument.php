<?php

namespace App\Contribution\Documents;

use App\Member\Member;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Zoomyboy\Tex\Engine;
use Zoomyboy\Tex\Template;

class SolingenDocument extends ContributionDocument
{
    /**
     * @param array<int, int> $members
     */
    final private function __construct(
        public string $dateFrom,
        public string $dateUntil,
        public string $zipLocation,
        public array $members,
        public string $eventName,
        public string $type = 'F',
    ) {
    }

    /**
     * @param array<string, mixed|int> $request
     */
    public static function fromRequest(array $request): static
    {
        return new static(
            dateFrom: $request['dateFrom'],
            dateUntil: $request['dateUntil'],
            zipLocation: $request['zipLocation'],
            members: $request['members'],
            eventName: $request['eventName'],
        );
    }

    /**
     * @return Collection<int, Collection<int, Member>>
     */
    public function memberModels(): Collection
    {
        return Member::whereIn('id', $this->members)->orderByRaw('lastname, firstname')->get()->toBase()->chunk(14);
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

    public static function getName(): string
    {
        return 'Für Stadt Solingen erstellen';
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
            'eventName' => 'required|string',
        ];
    }
}
