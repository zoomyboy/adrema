<?php

namespace App\Form\Actions;

use App\Form\Models\Participant;
use App\Lib\JobMiddleware\JobChannels;
use App\Lib\JobMiddleware\WithJobState;
use App\Lib\Queue\TracksJob;
use Lorisleiva\Actions\Concerns\AsAction;

class ParticipantDestroyAction
{
    use AsAction;
    use TracksJob;

    public function handle(int $participantId): void
    {
        Participant::findOrFail($participantId)->update(['cancelled_at' => now()]);
    }

    public function asController(Participant $participant): void
    {
        $this->startJob($participant->id);
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        return $jobState
            ->after('Teilnehmer gelöscht.')
            ->failed('Löschen von Teilnehmer fehlgeschlagen.')
            ->shouldReload(JobChannels::make()->add('participant'));
    }
}
