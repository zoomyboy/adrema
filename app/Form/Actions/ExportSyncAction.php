<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Group;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\TableDocument\SheetData;
use Zoomyboy\TableDocument\TableDocumentData;

class ExportSyncAction
{
    use AsAction;

    public Form $form;

    public function handle(Form $form): void
    {
        $this->form = $form;

        if (!$form->export->root) {
            return;
        }

        $storage = $form->export->root->getStorage();

        $storage->put($form->export->root->resource . '/Anmeldungen ' . $form->name . '.xlsx', file_get_contents($this->allSheet($this->form->participants)->compile($this->tempPath())));

        if ($form->export->toGroupField) {
            foreach ($form->participants->groupBy(fn ($participant) => $participant->data[$form->export->toGroupField]) as $groupId => $participants) {
                $group = Group::find($groupId);
                if (!$group?->fileshare) {
                    continue;
                }

                $group->fileshare->getStorage()->put($group->fileshare->resource . '/Anmeldungen ' . $form->name . '.xlsx', file_get_contents($this->allSheet($participants)->compile($this->tempPath())));
            }
        }
    }

    public function asJob(int $formId): void
    {
        $this->handle(Form::find($formId));
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
