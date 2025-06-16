<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use App\Lib\Events\Succeeded;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class FormCopyAction
{
    use AsAction;

    public function handle(Form $form): Form
    {
        $newForm = $form->replicate();
        $newForm->save();
        $newForm->update(['name' => $form->name.' - Kopie', 'is_active' => false]);

        foreach ($form->getRegisteredMediaCollections() as $collection) {
            foreach ($form->getMedia($collection->name) as $media) {
                $media->copy($newForm, $collection->name);
            }
        }

        ClearFrontendCacheAction::run();

        return $form;
    }

    public function asController(Form $form): JsonResponse
    {
        $this->handle($form);

        Succeeded::message('Veranstaltung kopiert.')->dispatch();
        return response()->json([]);
    }
}
