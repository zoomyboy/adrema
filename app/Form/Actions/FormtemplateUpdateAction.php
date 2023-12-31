<?php

namespace App\Form\Actions;

use App\Form\Models\Formtemplate;
use App\Lib\Events\Succeeded;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class FormtemplateUpdateAction
{
    use AsAction;
    use HasValidation;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...$this->globalRules(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getValidationAttributes(): array
    {
        return [
            ...$this->globalValidationAttributes(),
        ];
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function handle(Formtemplate $formtemplate, array $attributes): void
    {
        $formtemplate->update($attributes);
    }

    public function asController(Formtemplate $formtemplate, ActionRequest $request): JsonResponse
    {
        $this->handle($formtemplate, $request->validated());

        Succeeded::message('Vorlage aktualisiert.')->dispatch();

        return response()->json([]);
    }
}
