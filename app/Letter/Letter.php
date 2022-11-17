<?php

namespace App\Letter;

use App\Payment\Payment;
use Carbon\Carbon;
use Exception;
use Generator;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Zoomyboy\Tex\Document;
use Zoomyboy\Tex\Engine;
use Zoomyboy\Tex\Template;

abstract class Letter extends Document
{
    abstract public function getSubject(): string;

    abstract public function view(): string;

    abstract public function linkLabel(): string;

    abstract public function sendAllLabel(): string;

    /**
     * @param HasMany<Payment> $query
     *
     * @return HasMany<Payment>
     */
    abstract public static function paymentsQuery(HasMany $query): HasMany;

    /**
     * @return array<int, string>
     */
    abstract public function getDescription(): array;

    abstract public function afterSingle(Payment $payment): void;

    /**
     * @var Collection<int, Page>
     */
    public Collection $pages;
    public string $subject;
    protected string $filename;
    public string $until;
    public LetterSettings $settings;

    /**
     * @param Collection<int, Page> $pages
     */
    public function __construct(Collection $pages)
    {
        $this->pages = $pages;
        $this->subject = $this->getSubject();
        $this->until = now()->addWeeks(2)->format('d.m.Y');
        $this->setFilename(Str::slug("{$this->getSubject()} für {$pages->first()?->familyName}"));
        $this->settings = app(LetterSettings::class);
    }

    public function number(int $number): string
    {
        return number_format($number / 100, 2, '.', '');
    }

    public function getUntil(): Carbon
    {
        return now()->addWeeks(2);
    }

    public function getEngine(): Engine
    {
        return Engine::XELATEX;
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
        if (!$this->pages->first()?->email) {
            throw new Exception('Cannot get Recipient. Mail not set.');
        }

        return new MailRecipient($this->pages->first()->email, $this->pages->first()->familyName);
    }

    public function allPayments(): Generator
    {
        foreach ($this->pages as $page) {
            foreach ($page->getPayments() as $payment) {
                yield $payment;
            }
        }
    }

    /**
     * @return view-string
     */
    public function mailView(): string
    {
        $view = 'mail.payment.'.Str::snake(class_basename($this));

        if (!view()->exists($view)) {
            throw new Exception('Mail view '.$view.' existiert nicht.');
        }

        return $view;
    }
}
