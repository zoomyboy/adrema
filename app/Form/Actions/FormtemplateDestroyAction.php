<?php

namespace App\Form\Actions;

use App\Form\Models\Formtemplate;
use App\Lib\Events\Succeeded;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class FormtemplateDestroyAction
{
    use AsAction;

    public function handle(Formtemplate $formtemplate): void
    {
        $formtemplate->delete();
    }

    public function asController(Formtemplate $formtemplate): JsonResponse
    {
        $this->handle($formtemplate);

        Succeeded::message('Vorlage gelöscht.')->dispatch();

        return response()->json([]);
    }
}
