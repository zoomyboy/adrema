<?php

namespace App\Form\Actions;

use App\Form\Models\Formtemplate;
use App\Lib\Events\Succeeded;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class FormtemplateStoreAction
{
    use AsAction;
    use HasValidation;

    /**
     * @param array<string, mixed> $attributes
     */
    public function handle(array $attributes): Formtemplate
    {
        return Formtemplate::create($attributes);
    }

    public function asController(ActionRequest $request): JsonResponse
    {
        $this->handle($request->validated());

        Succeeded::message('Vorlage gespeichert.')->dispatch();
        return response()->json([]);
    }
}
