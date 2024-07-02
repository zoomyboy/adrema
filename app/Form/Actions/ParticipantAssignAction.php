<?php

namespace App\Form\Actions;

use App\Form\Models\Participant;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ParticipantAssignAction
{
    use AsAction;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'member_id' => 'required|exists:members,id',
        ];
    }

    public function handle(Participant $participant, ActionRequest $request): void
    {
        $participant->update(['member_id' => $request->input('member_id')]);
    }
}
