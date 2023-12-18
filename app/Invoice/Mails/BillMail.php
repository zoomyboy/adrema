<?php

namespace App\Invoice\Mails;

use App\Invoice\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BillMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public Invoice $invoice, public string $filename)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.invoice.bill')
            ->attach($this->filename)
            ->replyTo('kasse@stamm-silva.de')
            ->subject('Rechnung | DPSG Stamm Silva');
    }
}
