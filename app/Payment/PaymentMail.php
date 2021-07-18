<?php

namespace App\Payment;

use App\Pdf\PdfRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public PdfRepository $repo;
    public string $filename;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PdfRepository $repo, string $filename)
    {
        $this->filename = $filename;
        $this->repo = $repo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.payment.payment')
                    ->attach($this->filename)
                    ->replyTo('kasse@stamm-silva.de')
                    ->subject('Jahresrechnung | DPSG Stamm Silva');
    }
}
