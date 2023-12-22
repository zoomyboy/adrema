<?php

namespace App\Form\Actions;

use App\Form\Models\Formtemplate;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class FormtemplateStoreAction
{
    use AsAction;

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
    public function handle(array $attributes): Formtemplate
    {
        return Formtemplate::create($attributes);
    }

    public function asController(ActionRequest $request): JsonResponse
    {
        $this->handle($request->validated());

        return response()->json([]);
    }
}
