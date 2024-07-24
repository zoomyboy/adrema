<?php

namespace App\Form\Actions;

use App\Form\Scopes\FormFilterScope;
use App\Form\Models\Form;
use App\Form\Resources\FormApiResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class FormApiListAction
{
    use AsAction;

    /**
     * @param string $filter
     * @return LengthAwarePaginator<Form>
     */
    public function handle(string $filter, int $perPage): LengthAwarePaginator
    {
        return FormFilterScope::fromRequest($filter)->getQuery()->paginate($perPage);
    }

    public function asController(ActionRequest $request): AnonymousResourceCollection
    {
        return FormApiResource::collection($this->handle(
            $request->input('filter', ''),
            $request->input('perPage', 9999)
        ));
    }
}
