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
            'description' => 'required|string',
            'excerpt' => 'required|string|max:120',
            'from' => 'required|date',
            'to' => 'required|date',
            'registration_from' => 'present|nullable|date',
            'registration_until' => 'present|nullable|date',
            'mail_top' => 'nullable|string',
            'mail_bottom' => 'nullable|string',
        ];
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function handle(array $attributes): Form
    {
        return Form::create($attributes);
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
        ];
    }

    public function asController(ActionRequest $request): JsonResponse
    {
        $this->handle($request->validated());

        Succeeded::message('Formular gespeichert.')->dispatch();
        return response()->json([]);
    }
}
