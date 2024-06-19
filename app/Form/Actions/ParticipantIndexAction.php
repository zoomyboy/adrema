<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Resources\ParticipantResource;
use App\Form\Scopes\ParticipantFilterScope;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class ParticipantIndexAction
{
    use AsAction;

    /**
     * @return HasMany<Participant>
     */
    protected function getQuery(Form $form, ParticipantFilterScope $filter): HasMany
    {
        return $form->participants()->withFilter($filter)->withCount('children')->with('form');
    }

    /**
     * @return LengthAwarePaginator<Participant>
     */
    public function handle(Form $form, ParticipantFilterScope $filter): LengthAwarePaginator
    {
        return $this->getQuery($form, $filter)->paginate(15);
    }

    public function asController(Form $form, ?int $parent = null): AnonymousResourceCollection
    {
        $filter = ParticipantFilterScope::fromRequest(request()->input('filter'));

        $data = match ($parent) {
            null => $this->handle($form, $filter),
            -1 => $this->getQuery($form, $filter)->where('parent_id', null)->get(),
            default => $this->getQuery($form, $filter)->where('parent_id', $parent)->get(),
        };

        return ParticipantResource::collection($data)->additional(['meta' => ParticipantResource::meta($form)]);
    }
}
