<?php

namespace App\Form\Actions;

use App\Form\Data\FieldCollection;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Member\Member;
use Illuminate\Http\JsonResponse;
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
            ->reduce(fn ($query, $field) => $field->namiType->performQuery($query, $field->value), (new Member())->newQuery());
        $mglnr = $form->getFields()->withNamiType()->count() && $memberQuery->count() === 1 ? $memberQuery->first()->mitgliedsnr : null;

        $participant = $form->participants()->create([
            'data' => $input,
            'mitgliedsnr' => $mglnr,
        ]);

        $form->getFields()->each(fn ($field) => $field->afterRegistration($form, $participant, $input));

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
        $participant = $this->handle($form, $request->validated());

        return response()->json($participant);
    }
}
