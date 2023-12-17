<?php

namespace App\Invoice\Actions;

use App\Invoice\BillDocument;
use App\Invoice\Models\Invoice;
use App\Payment\Payment;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\Tex\BaseCompiler;
use Zoomyboy\Tex\Tex;

class DisplayPdfAction
{
    use AsAction;

    public function handle(Invoice $invoice): BaseCompiler|Response
    {
        return Tex::compile(BillDocument::fromInvoice($invoice));
    }
}
