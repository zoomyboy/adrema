<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Lib\Events\Succeeded;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ParticipantStoreAction
{
    use AsAction;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Form */
        $form = request()->route('form');

        return $form->getRegistrationRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function getValidationAttributes(): array
    {
        /** @var Form */
        $form = request()->route('form');

        return $form->getRegistrationAttributes();
    }

    /**
     * @return array<string, mixed>
     */
    public function getValidationMessages(): array
    {
        /** @var Form */
        $form = request()->route('form');

        return $form->getRegistrationMessages();
    }

    public function handle(Form $form, ActionRequest $request): JsonResponse
    {
        $form->participants()->create(['data' => $request->validated()]);
        ExportSyncAction::dispatch($form->id);
        Succeeded::message('Teilnehmer*in erstellt.')->dispatch();
        return response()->json([]);
    }
}
