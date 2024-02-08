<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use App\Form\Resources\ParticipantResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class ParticipantIndexAction
{
    use AsAction;

    public function handle(Form $form): LengthAwarePaginator
    {
        return $form->participants()->with('form')->paginate(15);
    }

    public function asController(Form $form): AnonymousResourceCollection
    {
        return ParticipantResource::collection($this->handle($form))
            ->additional(['meta' => ParticipantResource::meta($form)]);
    }
}
