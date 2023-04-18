<?php

namespace App\Invoice\Actions;

use App\Invoice\BillKind;
use App\Invoice\DocumentFactory;
use App\Invoice\Queries\BillKindQuery;
use App\Payment\PaymentMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\Tex\Tex;

class InvoiceSendAction
{
    use AsAction;

    /**
     * The name and signature of the console command.
     */
    public string $commandSignature = 'invoice:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Bills';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        foreach (app(DocumentFactory::class)->getTypes() as $type) {
            $invoices = app(DocumentFactory::class)->invoiceCollection($type, new BillKindQuery(BillKind::EMAIL));

            foreach ($invoices as $invoice) {
                $invoicePath = Storage::disk('temp')->path(Tex::compile($invoice)->storeIn('', 'temp'));
                Mail::to($invoice->getRecipient())
                    ->send(new PaymentMail($invoice, $invoicePath));
                app(DocumentFactory::class)->afterSingle($invoice);
            }
        }

        return 0;
    }
}
