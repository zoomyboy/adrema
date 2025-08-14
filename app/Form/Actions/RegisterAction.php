<?php

namespace App\Form\Actions;

use App\Form\Data\FieldCollection;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Member\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterAction
{
    use AsAction;

    /**
     * @param array<string, mixed> $input
     */
    public function handle(Form $form, array $input): Participant
    {
        $memberQuery = FieldCollection::fromRequest($form, $input)
            ->withNamiType()
            ->reduce(fn($query, $field) => $field->namiType->performQuery($query, $field->value), (new Member())->newQuery());
        $member = $form->getFields()->withNamiType()->count() && $memberQuery->count() === 1 ? $memberQuery->first() : null;

        $participant = $form->participants()->create([
            'data' => $input,
            'member_id' => $member?->id,
        ]);

        $form->getFields()->each(fn($field) => $field->afterRegistration($form, $participant, $input));

        $participant->sendConfirmationMail();
        ExportSyncAction::dispatch($form->id);

        return $participant;
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
        if (!$form->canRegister() && !$this->isRegisteringLater($request)) {
            throw ValidationException::withMessages(['event' => 'Anmeldung zzt nicht möglich.']);
        }

        $participant = $this->handle($form, $request->validated());

        return response()->json($participant);
    }

    public function isRegisteringLater(ActionRequest $request): bool {
        if (!is_array($request->query())) {
            return false;
        }

        $validator = Validator::make($request->query(), [
            'later' => 'required|numeric|in:1',
            'id' => 'required|string|uuid:4',
            'signature' => 'required|string',
        ]);

        return URL::hasValidSignature($request) && $validator->passes();
    }
}
