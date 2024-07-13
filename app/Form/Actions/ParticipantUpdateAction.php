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

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Participant */
        $participant = request()->route('participant');

        return $participant->form->getRegistrationRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function getValidationAttributes(): array
    {
        /** @var Participant */
        $participant = request()->route('participant');

        return $participant->form->getRegistrationAttributes();
    }

    /**
     * @return array<string, mixed>
     */
    public function getValidationMessages(): array
    {
        /** @var Participant */
        $participant = request()->route('participant');

        return $participant->form->getRegistrationMessages();
    }

    public function handle(Participant $participant, ActionRequest $request): JsonResponse
    {
        $participant->update(['data' => [...$participant->data, ...$request->validated()]]);
        ExportSyncAction::dispatch($participant->form->id);
        Succeeded::message('Teilnehmer*in bearbeitet.')->dispatch();
        return response()->json([]);
    }
}
