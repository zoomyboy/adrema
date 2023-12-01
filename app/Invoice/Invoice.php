<?php

namespace App\Invoice;

use App\Member\Member;
use App\Payment\Payment;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Zoomyboy\Tex\Document;
use Zoomyboy\Tex\Engine;
use Zoomyboy\Tex\Template;

abstract class Invoice extends Document
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
        public string $familyName,
        public string $singleName,
        public string $address,
        public string $zip,
        public string $location,
        public array $positions,
        public string $usage,
        public ?string $email,
    ) {
        $this->until = now()->addWeeks(2)->format('d.m.Y');
        $this->filename = Str::slug("{$this->getSubject()} für {$familyName}");
    }

    /**
     * @param Collection<(int|string), Member> $members
     */
    public static function fromMembers(Collection $members): self
    {
        return static::withoutMagicalCreationFrom([
            'familyName' => $members->first()->lastname,
            'singleName' => $members->first()->lastname,
            'address' => $members->first()->address,
            'zip' => $members->first()->zip,
            'location' => $members->first()->location,
            'email' => $members->first()->email_parents ?: $members->first()->email,
            'positions' => static::renderPositions($members),
            'usage' => "Mitgliedsbeitrag für {$members->first()->lastname}",
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
     * @param Collection<(int|string), Member> $members
     *
     * @return array<string, string>
     */
    public static function renderPositions(Collection $members): array
    {
        /** @var array<string, string> */
        $result = [];

        foreach ($members->pluck('payments')->flatten(1) as $payment) {
            if ($payment->subscription->split) {
                foreach ($payment->subscription->children as $child) {
                    $result["{$payment->subscription->name} ({$child->name}) {$payment->nr} für {$payment->member->firstname} {$payment->member->lastname}"] = static::number($child->amount);
                }
            } else {
                $result["{$payment->subscription->name} {$payment->nr} für {$payment->member->firstname} {$payment->member->lastname}"] = static::number($payment->subscription->getAmount());
            }
        }

        return $result;
    }

    public static function number(int $number): string
    {
        return number_format($number / 100, 2, '.', '');
    }
}
