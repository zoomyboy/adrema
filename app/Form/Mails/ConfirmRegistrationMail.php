<?php

namespace App\Form\Mails;

use App\Form\Data\FormConfigData;
use App\Form\Editor\FormConditionResolver;
use App\Form\Models\Participant;
use App\Lib\Editor\Condition;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $fullname;
    public FormConfigData $config;
    /** @var array<string, mixed> */
    public array $topText;
    /** @var array<string, mixed> */
    public array $bottomText;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public Participant $participant)
    {
        $conditionResolver = app(FormConditionResolver::class)->forParticipant($participant);
        $this->fullname = $participant->getFields()->getFullname();
        $this->config = $participant->getConfig();
        $this->topText = $conditionResolver->makeBlocks($participant->form->mail_top);
        $this->bottomText = $conditionResolver->makeBlocks($participant->form->mail_bottom);
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
     * @return array<int, Attachment>
     */
    public function attachments()
    {
        $conditionResolver = app(FormConditionResolver::class)->forParticipant($this->participant);

        return $this->participant->form->getMedia('mailattachments')
            ->filter(fn ($media) => $conditionResolver->filterCondition(Condition::fromMedia($media)))
            ->map(fn ($media) => Attachment::fromStorageDisk($media->disk, $media->getPathRelativeToRoot()))
            ->all();
    }
}
