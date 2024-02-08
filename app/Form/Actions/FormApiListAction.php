<?php

namespace App\Form\Actions;

use App\Form\FilterScope;
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
     * @param array<string, mixed> $filter
     * @return LengthAwarePaginator<Form>
     */
    public function handle(string $filter, int $perPage): LengthAwarePaginator
    {
        return FilterScope::fromRequest($filter)->getQuery()->paginate($perPage);
    }

    public function asController(ActionRequest $request): AnonymousResourceCollection
    {
        return FormApiResource::collection($this->handle(
            $request->input('filter', ''),
            $request->input('perPage', 10)
        ));
    }
}