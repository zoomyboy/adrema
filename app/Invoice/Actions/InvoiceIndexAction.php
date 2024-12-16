<?php

namespace App\Invoice\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Invoice\Models\Invoice;
use App\Invoice\Resources\InvoiceResource;
use App\Invoice\Scopes\InvoiceFilterScope;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Scout\Builder;
use Lorisleiva\Actions\ActionRequest;

class InvoiceIndexAction
{
    use AsAction;


    /**
     * @return Builder<Invoice>
     */
    public function handle(InvoiceFilterScope $filter): Builder
    {
        return $filter->getQuery()->query(fn ($q) => $q->with('positions'));
    }

    public function asController(ActionRequest $request): Response
    {
        session()->put('menu', 'invoice');
        session()->put('title', 'Rechnungen');

        $filter = InvoiceFilterScope::fromRequest($request->input('filter', ''));

        return Inertia::render('invoice/Index', [
            'data' => InvoiceResource::collection($this->handle($filter)->paginate(15)),
        ]);
    }
}
