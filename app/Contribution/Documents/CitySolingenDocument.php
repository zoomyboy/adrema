<?php

namespace App\Contribution\Documents;

use App\Contribution\Contracts\HasContributionData;
use App\Contribution\Data\MemberData;
use App\Invoice\InvoiceSettings;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Zoomyboy\Tex\Engine;

class CitySolingenDocument extends ContributionDocument
{
    public string $fromName;

    /**
     * @param Collection<int, MemberData> $members
     */
    final private function __construct(
        public Carbon $dateFrom,
        public Carbon $dateUntil,
        public string $zipLocation,
        public Collection $members,
        public string $eventName,
        public string $type = 'F',
    ) {
        $this->setEventName($eventName);
        $this->fromName = app(InvoiceSettings::class)->from_long;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromPayload(HasContributionData $request): static
    {
        return new static(
            dateFrom: $request->dateFrom(),
            dateUntil: $request->dateUntil(),
            zipLocation: $request->zipLocation(),
            members: $request->members(),
            eventName: $request->eventName(),
        );
    }

    /**
     * @return Collection<int, Collection<int, MemberData>>
     */
    public function memberModels(): Collection
    {
        return $this->members->chunk(14);
    }

    public function niceEventFrom(): string
    {
        return Carbon::parse($this->dateFrom)->format('d.m.Y');
    }

    public function niceEventUntil(): string
    {
        return Carbon::parse($this->dateUntil)->format('d.m.Y');
    }

    public function checkboxes(): string
    {
        $firstRow = collect(['B' => 'Jugendbildungsmaßnahme', 'G' => 'Gruppenleiter/innenschulung', 'FK' => 'Ferienkolonie', 'F' => 'Freizeitnaßnahme'])->map(function ($item, $key) {
            return ($this->type === $key ? '\\checkedcheckbox' : '\\checkbox') . '{' . $item . '}';
        })->implode(' & ') . ' \\\\';

        $secondRow = collect(['I' => 'Int. Jugendbegegnung', 'P' => 'politische Jugendbildung', 'PR' => 'Projekte'])->map(function ($item, $key) {
            return ($this->type === $key ? '\\checkedcheckbox' : '\\checkbox') . '{' . $item . '}';
        })->implode(' & ') . ' & \\emptycheckbox \\\\';

        return $firstRow . "\n" . $secondRow;
    }

    public function getEngine(): Engine
    {
        return Engine::PDFLATEX;
    }

    public static function getName(): string
    {
        return 'Stadt Solingen';
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
        ];
    }
}
