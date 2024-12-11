<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateParticipantSearchIndexAction
{
    use AsAction;

    public function handle(Form $form): void
    {
        $form->searchableUsing()->updateIndexSettings(
            $form->participantsSearchableAs(),
            [
                'filterableAttributes' => $form->getFields()->getKeys(),
                'searchableAttributes' => $form->getFields()->getKeys(),
                'sortableAttributes' => [],
                'displayedAttributes' => [...$form->getFields()->getKeys(), 'id'],
                'pagination' => [
                    'maxTotalHits' => 1000000,
                ]
            ]
        );
    }
}
