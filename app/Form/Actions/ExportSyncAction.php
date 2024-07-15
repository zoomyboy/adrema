<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use App\Group;
use Lorisleiva\Actions\Concerns\AsAction;

class ExportSyncAction
{
    use AsAction;

    public Form $form;

    public function handle(Form $form): void
    {
        if (!$form->export->root) {
            return;
        }

        $storage = $form->export->root->getStorage();

        $storage->put($form->export->root->resource . '/Anmeldungen ' . $form->name . '.xlsx', CreateExcelDocumentAction::run($form, $form->participants));

        if ($form->export->toGroupField) {
            foreach ($form->participants->groupBy(fn ($participant) => $participant->data[$form->export->toGroupField]) as $groupId => $participants) {
                $group = Group::find($groupId);
                if (!$group?->fileshare) {
                    continue;
                }

                $group->fileshare->getStorage()->put($group->fileshare->resource . '/Anmeldungen ' . $form->name . '.xlsx', CreateExcelDocumentAction::run($form, $participants));
            }
        }
    }

    public function asJob(int $formId): void
    {
        $this->handle(Form::find($formId));
    }
}
