<?php

namespace App\Invoice\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Invoice\Models\Invoice;
use App\Invoice\Resources\InvoiceResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceIndexAction
{
    use AsAction;


    /**
     * @return LengthAwarePaginator<Invoice>
     */
    public function handle(): LengthAwarePaginator
    {
        return Invoice::select('*')->with('positions')->paginate(15);
    }

    public function asController(): Response
    {
        return Inertia::render('invoice/Index', [
            'data' => InvoiceResource::collection($this->handle()),
        ]);
    }
}
