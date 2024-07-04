<?php

namespace App\Prevention\Mails;

use App\Invoice\InvoiceSettings;
use App\Lib\Editor\EditorData;
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

    /**
     * Create a new message instance.
     */
    public function __construct(public Preventable $preventable, public EditorData $bodyText)
    {
        $this->settings = app(InvoiceSettings::class);
        $this->bodyText = $this->bodyText
            ->replaceWithList('wanted', collect($preventable->preventions())->map(fn ($prevention) => $prevention->text())->toArray());
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
