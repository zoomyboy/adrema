<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateParticipantSearchIndexAction
{
    use AsAction;

    public function handle(Form $form): void
    {
        if (config('scout.driver') !== 'meilisearch') {
            return;
        }

        $form->searchableUsing()->updateIndexSettings(
            $form->participantsSearchableAs(),
            [
                'filterableAttributes' => [...$form->getFields()->filterables()->getKeys(), 'parent-id', 'cancelled_at'],
                'searchableAttributes' => $form->getFields()->searchables()->getKeys(),
                'sortableAttributes' => [...$form->getFields()->sortables()->getKeys(), 'id', 'created_at'],
                'displayedAttributes' => [...$form->getFields()->filterables()->getKeys(), ...$form->getFields()->searchables()->getKeys(), 'id', 'cancelled_at'],
                'pagination' => [
                    'maxTotalHits' => 1000000,
                ]
            ]
        );
    }
}
