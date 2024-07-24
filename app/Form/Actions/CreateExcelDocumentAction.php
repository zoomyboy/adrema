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
        $headers = $this->form->getFields()->map(fn ($field) => $field->name)->toArray();

        $document->addSheet(SheetData::from([
            'header' => $headers,
            'data' => $participants
                ->map(fn ($participant) => $this->form->getFields()->map(fn ($field) => $participant->getFields()->find($field)->presentRaw())->toArray())
                ->toArray(),
            'name' => 'Alle',
        ]));

        if ($this->form->export->groupBy) {
            $groups = $participants->groupBy(fn ($participant) => $participant->getFields()->findByKey($this->form->export->groupBy)->presentRaw());

            foreach ($groups as $name => $participants) {
                $document->addSheet(SheetData::from([
                    'header' => $headers,
                    'data' => $participants
                        ->map(fn ($participant) => $this->form->getFields()->map(fn ($field) => $participant->getFields()->find($field)->presentRaw())->toArray())
                        ->toArray(),
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

    private function tempPath(): string
    {
        return sys_get_temp_dir() . '/' . str()->uuid()->toString();
    }
}
