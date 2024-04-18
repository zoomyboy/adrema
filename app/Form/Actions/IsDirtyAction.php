<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class IsDirtyAction
{
    use AsAction;

    public function handle(Form $form, ActionRequest $request): JsonResponse
    {
        $form->config = $request->input('config');

        return response()->json([
            'result' => $form->isDirty('config'),
        ]);
    }
}
