<?php

namespace App\Form\Actions;

use App\Form\Models\Participant;
use App\Lib\Events\Succeeded;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ParticipantUpdateAction
{
    use AsAction;

    public function rules(): array
    {
        return [
            'data' => 'required',
        ];
    }

    public function handle(Participant $participant, ActionRequest $request): JsonResponse
    {
        $participant->update(['data' => [...$participant->data, ...$request->validated('data')]]);

        Succeeded::message('Teilnehmer*in bearbeitet.')->dispatch();
        return response()->json([]);
    }
}
