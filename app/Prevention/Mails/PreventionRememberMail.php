<?php

namespace App\Prevention\Mails;

use App\Invoice\InvoiceSettings;
use App\Prevention\Contracts\Preventable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PreventionRememberMail extends Mailable
{
    use Queueable, SerializesModels;

    public InvoiceSettings $settings;
    public string $documents;

    /**
     * Create a new message instance.
     */
    public function __construct(public Preventable $preventable)
    {
        $this->settings = app(InvoiceSettings::class);
        $this->documents = collect($preventable->preventions())->map(fn ($prevention) => "* {$prevention->text()}")->implode("\n");
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return (new Envelope(
            subject: $this->preventable->preventableSubject(),
        ))->to($this->preventable->getMailRecipient()->email, $this->preventable->getMailRecipient()->name);
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: $this->preventable->preventableLayout(),
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
