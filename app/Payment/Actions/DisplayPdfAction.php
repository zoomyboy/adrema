<?php

namespace App\Payment\Actions;

use App\Invoice\BillDocument;
use App\Payment\Payment;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\Tex\BaseCompiler;
use Zoomyboy\Tex\Tex;

class DisplayPdfAction
{
    use AsAction;

    public function handle(Payment $payment): BaseCompiler|Response
    {
        if (null === $payment->invoice_data) {
            return response()->noContent();
        }

        $invoice = BillDocument::from($payment->invoice_data);

        return Tex::compile($invoice);
    }
}
