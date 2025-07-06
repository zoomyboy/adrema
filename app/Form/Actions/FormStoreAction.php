<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use App\Lib\Events\Succeeded;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\ActionRequest;

class FormStoreAction
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
            'description.time' => 'required|integer',
            'description.blocks' => 'required|array',
            'description.version' => 'required|string',
            'excerpt' => 'required|string|max:130',
            'from' => 'required|date',
            'to' => 'required|date',
            'registration_from' => 'present|nullable|date',
            'registration_until' => 'present|nullable|date',
            'header_image' => 'required|exclude',
            'mailattachments' => 'present|array|exclude',
            'is_active' => 'boolean',
            'is_private' => 'boolean',
            'export' => 'nullable|array',
            'needs_prevention' => 'present|boolean',
            'prevention_text' => 'array',
            'prevention_conditions' => 'array',
            'zip' => 'present|nullable|string',
            'location' => 'present|nullable|string',
            'country' => 'nullable|string|max:255',
        ];
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function handle(array $attributes): Form
    {
        return tap(Form::create($attributes), function ($form) {
            $form->setDeferredUploads(request()->input('header_image'));
            $form->setDeferredUploads(request()->input('mailattachments'));
            ClearFrontendCacheAction::run();
        });
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
            'header_image' => 'Bild',
            'description.blocks' => 'Beschreibung',
        ];
    }

    public function asController(ActionRequest $request): JsonResponse
    {
        $this->handle($request->validated());

        Succeeded::message('Veranstaltung gespeichert.')->dispatch();
        return response()->json([]);
    }
}
