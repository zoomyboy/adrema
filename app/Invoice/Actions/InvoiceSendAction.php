<?php

namespace App\Invoice\Actions;

use App\Invoice\BillDocument;
use App\Invoice\BillKind;
use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Mails\BillMail;
use App\Invoice\Mails\RememberMail;
use App\Invoice\Models\Invoice;
use App\Invoice\RememberDocument;
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
        foreach (Invoice::whereNeedsBill()->where('via', BillKind::EMAIL)->get() as $invoice) {
            $document = BillDocument::fromInvoice($invoice);
            $path = Storage::disk('temp')->path(Tex::compile($document)->storeIn('', 'temp'));
            Mail::to($invoice->getMailRecipient())->send(new BillMail($invoice, $path));
            $invoice->sent($document);
        }

        foreach (Invoice::whereNeedsRemember()->where('via', BillKind::EMAIL)->get() as $invoice) {
            $document = RememberDocument::fromInvoice($invoice);
            $path = Storage::disk('temp')->path(Tex::compile($document)->storeIn('', 'temp'));
            Mail::to($invoice->getMailRecipient())->send(new RememberMail($invoice, $path));
            $invoice->sent($document);
        }

        return 0;
    }
}
