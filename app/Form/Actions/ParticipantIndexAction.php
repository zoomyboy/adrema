<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Resources\ParticipantResource;
use App\Form\Scopes\ParticipantFilterScope;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class ParticipantIndexAction
{
    use AsAction;

    /**
     * @return LengthAwarePaginator<Participant>
     */
    public function handle(Form $form, ParticipantFilterScope $filter): LengthAwarePaginator
    {
        return $form->participants()->withFilter($filter)->with('form')->paginate(15);
    }

    public function asController(Form $form): AnonymousResourceCollection
    {
        $filter = ParticipantFilterScope::fromRequest(request()->input('filter'));
        return ParticipantResource::collection($this->handle($form, $filter))
            ->additional(['meta' => ParticipantResource::meta($form)]);
    }
}
