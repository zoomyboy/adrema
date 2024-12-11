<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Resources\ParticipantResource;
use App\Form\Scopes\ParticipantFilterScope;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Laravel\Scout\Builder;
use Lorisleiva\Actions\Concerns\AsAction;

class ParticipantIndexAction
{
    use AsAction;

    /**
     * @return Builder<Participant>
     */
    protected function getQuery(Form $form, ParticipantFilterScope $filter): Builder
    {
        return $filter->setForm($form)->getQuery()
            ->query(fn ($q) => $q->withCount('children')->with('form'));
    }

    public function asController(Form $form, ?int $parent = null): AnonymousResourceCollection
    {
        $filter = ParticipantFilterScope::fromRequest(request()->input('filter', ''))->parent($parent);

        $data = match ($parent) {
            null => $this->getQuery($form, $filter)->paginate(15),                              // initial all elements - paginate
            -1 => $this->getQuery($form, $filter)->paginate(15),      // initial root elements - parinate
            default => $this->getQuery($form, $filter)->get(),     // specific parent element - show all
        };

        return ParticipantResource::collection($data)->additional(['meta' => ParticipantResource::meta($form)]);
    }
}
