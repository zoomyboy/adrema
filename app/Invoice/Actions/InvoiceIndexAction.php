<?php

namespace App\Invoice\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Invoice\Models\Invoice;
use App\Invoice\Resources\InvoiceResource;
use App\Invoice\Scopes\InvoiceFilterScope;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\ActionRequest;

class InvoiceIndexAction
{
    use AsAction;


    /**
     * @return LengthAwarePaginator<Invoice>
     */
    public function handle(InvoiceFilterScope $filter): LengthAwarePaginator
    {
        return Invoice::withFilter($filter)->with('positions')->paginate(15);
    }

    public function asController(ActionRequest $request): Response
    {
        session()->put('menu', 'invoice');
        session()->put('title', 'Rechnungen');

        $filter = InvoiceFilterScope::fromRequest($request->input('filter', ''));

        return Inertia::render('invoice/Index', [
            'data' => InvoiceResource::collection($this->handle($filter)),
        ]);
    }
}
