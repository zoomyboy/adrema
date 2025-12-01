<?php

namespace App\Form\Actions;

use App\Form\Scopes\FormFilterScope;
use App\Form\Models\Form;
use App\Form\Resources\FormResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class FormIndexAction
{
    use AsAction;

    /**
     * @return LengthAwarePaginator<int, Form>
     */
    public function handle(string $filter): LengthAwarePaginator
    {
        return FormFilterScope::fromRequest($filter)->getQuery()->query(fn ($query) => $query->withCount(['participants' => fn ($q) => $q->whereNull('cancelled_at')]))->paginate(15);
    }

    public function asController(ActionRequest $request): Response
    {
        session()->put('menu', 'form');
        session()->put('title', 'Veranstaltungen');

        return Inertia::render('form/Index', [
            'data' => FormResource::collection($this->handle($request->input('filter', ''))),
        ]);
    }
}
