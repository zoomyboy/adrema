<?php

namespace App\Payment;

use App\Pdf\PdfRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class PaymentMail extends Mailable
{
    use Queueable;
    use SerializesModels;

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
        $template = Str::snake(class_basename($this->repo));

        return $this->markdown('mail.payment.'.$template)
                    ->attach($this->filename)
                    ->replyTo('kasse@stamm-silva.de')
                    ->subject($this->repo->getMailSubject().' | DPSG Stamm Silva');
    }
}
