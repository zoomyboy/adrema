<?php

namespace App\Invoice;

use App\Invoice\Models\Invoice;
use App\Payment\Payment;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Zoomyboy\Tex\Document;
use Zoomyboy\Tex\Engine;
use Zoomyboy\Tex\Template;

abstract class InvoiceDocument extends Document
{
    abstract public function getSubject(): string;
    abstract public function view(): string;
    abstract public function afterSingle(Payment $payment): void;
    abstract public function linkLabel(): string;
    abstract public static function sendAllLabel(): string;

    /**
     * @param HasMany<Payment> $query
     *
     * @return HasMany<Payment>
     */
    abstract public static function paymentsQuery(HasMany $query): HasMany;

    /**
     * @return array<int, string>
     */
    abstract public static function getDescription(): array;

    public string $until;
    public string $filename;

    /**
     * @param array<string, string> $positions
     */
    public function __construct(
        public string $toName,
        public string $toAddress,
        public string $toZip,
        public string $toLocation,
        public string $greeting,
        public array $positions,
        public string $usage,
    ) {
        $this->until = now()->addWeeks(2)->format('d.m.Y');
        $this->filename = Str::slug("{$this->getSubject()} für {$toName}");
    }

    public static function fromInvoice(Invoice $invoice): self
    {
        return static::withoutMagicalCreationFrom([
            'toName' => $invoice->to['name'],
            'toAddress' => $invoice->to['address'],
            'toZip' => $invoice->to['zip'],
            'toLocation' => $invoice->to['location'],
            'greeting' => $invoice->greeting,
            'positions' => static::renderPositions($invoice),
            'usage' => $invoice->usage,
        ]);
    }

    public function settings(): InvoiceSettings
    {
        return app(InvoiceSettings::class);
    }

    public function getEngine(): Engine
    {
        return Engine::PDFLATEX;
    }

    public function basename(): string
    {
        return $this->filename;
    }

    public function template(): Template
    {
        return Template::make('tex.templates.default');
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getRecipient(): MailRecipient
    {
        throw_unless($this->email, Exception::class, 'Cannot get Recipient. Mail not set.');

        return new MailRecipient($this->email, $this->familyName);
    }

    /**
     * @return view-string
     */
    public function mailView(): string
    {
        $view = 'mail.payment.' . Str::snake(class_basename($this));

        throw_unless(view()->exists($view), Exception::class, 'Mail view ' . $view . ' existiert nicht.');

        return $view;
    }

    /**
     * @return array<string, string>
     */
    public static function renderPositions(Invoice $invoice): array
    {
        return $invoice->positions->mapWithKeys(fn ($position) => [$position->description => static::number($position->price)])->toArray();
    }

    public static function number(int $number): string
    {
        return number_format($number / 100, 2, '.', '');
    }
}
