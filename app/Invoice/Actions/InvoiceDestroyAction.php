<?php

namespace App\Invoice\Actions;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Invoice\Models\Invoice;
use App\Lib\Events\Succeeded;
use Illuminate\Http\JsonResponse;

class InvoiceDestroyAction
{
    use AsAction;

    public function handle(Invoice $invoice): JsonResponse
    {
        $invoice->delete();

        Succeeded::message('Rechnung gelöscht.')->dispatch();

        return response()->json([]);
    }
}
