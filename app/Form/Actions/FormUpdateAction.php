<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use App\Lib\Events\Succeeded;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\ActionRequest;

class FormUpdateAction
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
            'description' => 'required|array',
            'description.time' => 'required|integer',
            'description.blocks' => 'required|array',
            'description.version' => 'required|string',
            'excerpt' => 'required|string|max:130',
            'from' => 'required|date',
            'to' => 'required|date',
            'registration_from' => 'present|nullable|date',
            'registration_until' => 'present|nullable|date',
            'is_active' => 'boolean',
            'is_private' => 'boolean',
            'export' => 'nullable|array',
            'needs_prevention' => 'present|boolean',
            'prevention_text' => 'array',
            'prevention_conditions' => 'array',
            'location' => 'present|nullable|string',
            'zip' => 'present|nullable|string',
        ];
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function handle(Form $form, array $attributes): Form
    {
        $form->update($attributes);

        ClearFrontendCacheAction::run();

        return $form;
    }

    /**
     * @return array<string, mixed>
     */
    public function getValidationAttributes(): array
    {
        return [
            ...$this->globalValidationAttributes(),
            'from' => 'Start',
            'to' => 'Ende',
            'description.blocks' => 'Beschreibung',
        ];
    }

    public function asController(Form $form, ActionRequest $request): JsonResponse
    {
        $this->handle($form, $request->validated());

        Succeeded::message('Veranstaltung aktualisiert.')->dispatch();
        return response()->json([]);
    }
}
