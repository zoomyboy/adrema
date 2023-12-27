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
        ];
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function handle(array $attributes): Form
    {
        return Form::create($attributes);
    }

    public function asController(ActionRequest $request): JsonResponse
    {
        $this->handle($request->validated());

        Succeeded::message('Formular gespeichert.')->dispatch();
        return response()->json([]);
    }
}
