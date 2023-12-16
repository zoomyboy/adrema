<?php

namespace App\Invoice\Actions;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Invoice\Models\Invoice;
use App\Lib\Events\Succeeded;
use Illuminate\Support\Arr;

class InvoiceUpdateAction
{
    use AsAction;
    use HasValidation;

    public function handle(Invoice $invoice, ActionRequest $request): void
    {
        $invoice->update($request->safe()->except('positions'));

        foreach ($request->validated('positions') as $position) {
            if ($position['id']) {
                $invoice->positions()->firstWhere('id', $position['id'])->update(Arr::except($position, 'id'));
                continue;
            }

            $invoice->positions()->create($position);
        }

        $invoice->positions()->whereNotIn('id', array_column($request->validated('positions'), 'id'))->delete();

        Succeeded::message('Rechnung bearbeitet.')->dispatch();
    }
}
