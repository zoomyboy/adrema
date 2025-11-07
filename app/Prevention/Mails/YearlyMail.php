<?php

namespace App\Prevention\Mails;

use App\Invoice\InvoiceSettings;
use App\Lib\Editor\EditorData;
use App\Prevention\Contracts\Preventable;
use App\Prevention\Data\PreventionData;
use App\Prevention\PreventionSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class YearlyMail extends Mailable
{
    use Queueable, SerializesModels;

    public InvoiceSettings $settings;
    public PreventionSettings $preventionSettings;

    /**
     * Create a new message instance.
     * @param Collection<int, PreventionData> $preventions
     */
    public function __construct(public Preventable $preventable, public EditorData $bodyText, public Collection $preventions)
    {
        $this->settings = app(InvoiceSettings::class);
        $this->preventionSettings = app(PreventionSettings::class);
        $this->bodyText = $this->bodyText
            ->replaceWithList('wanted', $preventions->map(fn($prevention) => $prevention->text())->toArray());
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $envelope = (new Envelope(
            subject: $this->preventable->preventableSubject(),
        ))->to($this->preventable->getMailRecipient()->email, $this->preventable->getMailRecipient()->name);

        if ($this->preventionSettings->replyToMail !== null) {
            $envelope->replyTo($this->preventionSettings->replyToMail);
        }

        return $envelope;
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'mail.prevention.prevention-remember-participant',
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
