<?php

namespace App\Invoice\Mails;

use App\Invoice\InvoiceSettings;
use App\Invoice\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BillMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public InvoiceSettings $settings;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public Invoice $invoice, public string $filename)
    {
        $this->settings = app(InvoiceSettings::class);
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
            ->when($this->settings->replyTo, fn ($mail) => $mail->replyTo($this->settings->replyTo))
            ->subject('Rechnung | '.$this->settings->from_long);
    }
}
