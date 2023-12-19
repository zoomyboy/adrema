<?php

namespace App\Invoice\Actions;

use App\Invoice\BillDocument;
use App\Invoice\BillKind;
use App\Invoice\Models\Invoice;
use App\Invoice\RememberDocument;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\Tex\BaseCompiler;
use Zoomyboy\Tex\Tex;

class MassPostPdfAction
{
    use AsAction;

    public function handle(): BaseCompiler|Response
    {
        $documents = [];

        foreach (Invoice::whereNeedsBill()->where('via', BillKind::POST)->get() as $invoice) {
            $document = BillDocument::fromInvoice($invoice);
            $documents[] = $document;
            $invoice->sent($document);
        }

        foreach (Invoice::whereNeedsRemember()->where('via', BillKind::POST)->get() as $invoice) {
            $document = RememberDocument::fromInvoice($invoice);
            $documents[] = $document;
            $invoice->sent($document);
        }

        if (!count($documents)) {
            return response()->noContent();
        }

        return Tex::merge($documents);
    }
}
