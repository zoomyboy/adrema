<?php

namespace App\Invoice\Actions;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Invoice\Models\Invoice;
use App\Lib\Events\Succeeded;

class InvoiceStoreAction
{
    use AsAction;
    use HasValidation;

    public function handle(ActionRequest $request): void
    {
        $invoice = Invoice::create($request->safe()->except('positions'));

        foreach ($request->validated('positions') as $position) {
            $invoice->positions()->create($position);
        }

        Succeeded::message('Rechnung erstellt.')->dispatch();
    }
}
