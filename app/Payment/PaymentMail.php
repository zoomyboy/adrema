<?php

namespace App\Payment;

use App\Invoice\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public Invoice $invoice;
    public string $filename;
    public string $salutation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice, string $filename)
    {
        $this->invoice = $invoice;
        $this->filename = $filename;
        $this->salutation = 'Liebe Familie ' . $invoice->familyName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown($this->invoice->mailView())
            ->attach($this->filename)
            ->replyTo('kasse@stamm-silva.de')
            ->subject($this->invoice->getSubject() . ' | DPSG Stamm Silva');
    }
}
