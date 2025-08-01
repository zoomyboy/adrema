<?php

namespace App\Form\Actions;

use App\Form\Editor\FormConditionResolver;
use App\Form\Models\Participant;
use App\Prevention\Mails\PreventionRememberMail;
use App\Prevention\PreventionSettings;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;

class PreventionRememberAction
{
    use AsAction;

    public string $commandSignature = 'prevention:remember-forms';

    public function handle(): void
    {
        $query = Participant::whereHas(
            'form',
            fn($form) => $form
                ->where('needs_prevention', true)
                ->where('from', '>=', now())
        )
            ->where(
                fn($q) => $q
                    ->where('last_remembered_at', '<=', now()->subWeeks(2))
                    ->orWhereNull('last_remembered_at')
            );
        foreach ($query->get() as $participant) {
            if (!app(FormConditionResolver::class)->forParticipant($participant)->filterCondition($participant->form->prevention_conditions)) {
                continue;
            }

            if ($participant->getFields()->getMailRecipient() === null || $participant->preventions()->count() === 0) {
                continue;
            }

            $body = app(PreventionSettings::class)->refresh()->formmail
                ->placeholder('formname', $participant->form->name)
                ->append($participant->form->prevention_text);

            Mail::send(new PreventionRememberMail($participant, $body, $participant->preventions()));

            $participant->update(['last_remembered_at' => now()]);
        }
    }
}
