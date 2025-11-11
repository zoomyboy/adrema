<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\TableDocument\SheetData;
use Zoomyboy\TableDocument\TableDocumentData;

class CreateExcelDocumentAction
{
    use AsAction;

    public Form $form;

    /**
     * @param Collection<int, Participant> $participants
     */
    public function handle(Form $form, Collection $participants): string
    {
        $this->form = $form;

        return file_get_contents($this->allSheet($participants)->compile($this->tempPath()));
    }

    /**
     * @param Collection<int, Participant> $participants
     */
    private function allSheet(Collection $participants): TableDocumentData
    {
        $document = TableDocumentData::from(['title' => 'Anmeldungen für ' . $this->form->name, 'sheets' => []]);
        $headers = $this->form->getFields()->map(fn ($field) => $field->name)->push('Abgemeldet am')->prepend('ID')->toArray();
        [$activeParticipants, $cancelledParticipants] = $participants->partition(fn ($participant) => $participant->cancelled_at === null);

        $document->addSheet(SheetData::from([
            'header' => $headers,
            'data' => $activeParticipants->map(fn ($participant) => $this->rowFor($participant))->toArray(),
            'name' => 'Alle',
        ]));
        $document->addSheet(SheetData::from([
            'header' => $headers,
            'data' => $cancelledParticipants->map(fn ($participant) => $this->rowFor($participant))->toArray(),
            'name' => 'Abgemeldet',
        ]));

        if ($this->form->export->groupBy) {
            $groups = $activeParticipants->groupBy(fn ($participant) => $participant->getFields()->findByKey($this->form->export->groupBy)->presentRaw());

            foreach ($groups as $name => $groupedParticipants) {
                $document->addSheet(SheetData::from([
                    'header' => $headers,
                    'data' => $groupedParticipants->map(fn ($participant) => $this->rowFor($participant))->toArray(),
                    'name' => $name,
                ]));
            }

            $document->addSheet(SheetData::from([
                'header' => ['Wert', 'Anzahl'],
                'data' => $groups->map(fn ($participants, $name) => [$name, (string) count($participants)])->toArray(),
                'name' => 'Statistik',
            ]));
        }

        return $document;
    }

    /** @return array<string, mixed> */
    public function rowFor(Participant $participant): array {
        return $this->form->getFields()->map(fn ($field) => $participant->getFields()->find($field)->presentRaw())
            ->put('Abgemeldet am', $participant->cancelled_at?->format('d.m.Y H:i:s') ?: '')
            ->prepend('ID', $participant->id)
            ->toArray();
    }

    private function tempPath(): string
    {
        return sys_get_temp_dir() . '/' . str()->uuid()->toString();
    }
}
