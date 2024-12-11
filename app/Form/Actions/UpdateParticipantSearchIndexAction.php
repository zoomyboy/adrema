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
                'filterableAttributes' => [...$form->getFields()->filterables()->getKeys(), 'parent-id'],
                'searchableAttributes' => $form->getFields()->searchables()->getKeys(),
                'sortableAttributes' => [],
                'displayedAttributes' => [...$form->getFields()->filterables()->getKeys(), ...$form->getFields()->searchables()->getKeys(), 'id'],
                'pagination' => [
                    'maxTotalHits' => 1000000,
                ]
            ]
        );
    }
}
