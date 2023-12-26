<?php

namespace App\Form\Actions;

use App\Form\Models\Formtemplate;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class FormtemplateUpdateAction
{
    use AsAction;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'config' => '',
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

        return response()->json([]);
    }
}
