<?php

namespace App\Form\Mails;

use App\Form\Data\FormConfigData;
use App\Form\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $fullname;
    public FormConfigData $config;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public Participant $participant)
    {
        $this->fullname = $participant->getFields()->getFullname();
        $this->config = $participant->getConfig();
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Deine Anmeldung zu ' . $this->participant->form->name,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'mail.form.confirm-registration',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
