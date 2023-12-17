<?php

namespace App\Invoice\Actions;

use App\Invoice\Models\Invoice;
use App\Invoice\RememberDocument;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\Tex\BaseCompiler;
use Zoomyboy\Tex\Tex;

class DisplayRememberpdfAction
{
    use AsAction;

    public function handle(Invoice $invoice): BaseCompiler|Response
    {
        return Tex::compile(RememberDocument::fromInvoice($invoice));
    }
}
