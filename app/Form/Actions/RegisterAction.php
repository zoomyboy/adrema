<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterAction
{
    use AsAction;

    public function handle(Form $form, array $input): Participant
    {
        return $form->participants()->create([
            'data' => $input
        ]);
    }

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

    public function asController(ActionRequest $request, Form $form): JsonResponse
    {
        $participant = $this->handle($form, $request->validated());

        return response()->json($participant);
    }
}
