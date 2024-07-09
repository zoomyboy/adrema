<?php

namespace App\Form\Actions;

use App\Form\Models\Participant;
use App\Prevention\Mails\PreventionRememberMail;
use App\Prevention\PreventionSettings;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;

class PreventionRememberAction
{
    use AsAction;

    public string $commandSignature = 'prevention:remember';

    public function handle(): void
    {
        $query = Participant::whereHas('form', fn ($form) => $form->where('needs_prevention', true))
            ->where(
                fn ($q) => $q
                    ->where('last_remembered_at', '<=', now()->subWeeks(2))
                    ->orWhereNull('last_remembered_at')
            );
        foreach ($query->get() as $participant) {
            if ($participant->getFields()->getMailRecipient() === null || count($participant->preventions()) === 0) {
                continue;
            }

            $body = app(PreventionSettings::class)->refresh()->formmail
                ->placeholder('formname', $participant->form->name)
                ->append($participant->form->prevention_text);

            Mail::send(new PreventionRememberMail($participant, $body));

            $participant->update(['last_remembered_at' => now()]);
        }
    }
}
