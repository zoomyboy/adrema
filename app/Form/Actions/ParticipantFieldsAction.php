<?php

namespace App\Form\Actions;

use App\Form\Models\Participant;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class ParticipantFieldsAction
{
    use AsAction;

    public function handle(Participant $participant): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $participant->id,
                'config' => $participant->getConfig(),
            ]
        ]);
    }
}
