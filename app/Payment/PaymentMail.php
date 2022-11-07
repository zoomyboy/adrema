<?php

namespace App\Payment;

use App\Letter\Letter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public Letter $letter;
    public string $filename;
    public string $salutation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Letter $letter, string $filename)
    {
        $this->letter = $letter;
        $this->filename = $filename;
        $this->salutation = 'Liebe Familie '.$letter->pages->first()->familyName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown($this->letter->mailView())
                    ->attach($this->filename)
                    ->replyTo('kasse@stamm-silva.de')
                    ->subject($this->letter->getSubject().' | DPSG Stamm Silva');
    }
}
