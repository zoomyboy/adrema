<?php

namespace App\Form\Actions;

use App\Form\FilterScope;
use App\Form\Models\Form;
use App\Form\Resources\FormApiResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class FormApiListAction
{
    use AsAction;

    /**
     * @return Collection<int, Form>
     */
    public function handle(FilterScope $filter): Collection
    {
        return Form::withFilter($filter)->get();
    }

    public function asController(ActionRequest $request): AnonymousResourceCollection
    {
        return FormApiResource::collection($this->handle(FilterScope::fromRequest($request->input('filter', ''))));
    }
}
