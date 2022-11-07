<?php

namespace App\Letter;

use App\Payment\Payment;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class RememberDocument extends Letter
{
    public function linkLabel(): string
    {
        return 'Erinnerung erstellen';
    }

    public function getSubject(): string
    {
        return 'Zahlungserinnerung';
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function view(): string
    {
        return 'tex.remember';
    }

    /**
     * @return array<string, string>
     */
    public function getPositions(Collection $page): array
    {
        $memberIds = $page->pluck('id')->toArray();
        $payments = Payment::whereIn('member_id', $memberIds)
            ->orderByRaw('nr, member_id')->whereNeedsRemember()->get();

        return $payments->mapWithKeys(function (Payment $payment) {
            $key = "Beitrag {$payment->nr} für {$payment->member->firstname} {$payment->member->lastname} ({$payment->subscription->name})";

            return [$key => $this->number($payment->subscription->amount)];
        })->toArray();
    }

    public function getAddress(Collection $page): string
    {
        return $page->first()->address;
    }

    public function getZip(Collection $page): string
    {
        return $page->first()->zip;
    }

    public function getEmail(Collection $page): string
    {
        return $page->first()->email_parents ?: $page->first()->email;
    }

    public function getLocation(Collection $page): string
    {
        return $page->first()->location;
    }

    public function sendAllLabel(): string
    {
        return 'Erinnerungen versenden';
    }

    /**
     * Get Descriptions for sendpayment page.
     *
     * @return array<int, string>
     */
    public function getDescription(): array
    {
        return [
            'Diese Funktion erstellt Erinnerungs-PDFs mit allen versendeten aber noch nich bezahlten Rechnungen bei den Mitgliedern die Post als Versandweg haben.',
            'Das zuletzt erinnerte Datum wird auf heute gesetzt.',
        ];
    }

    public function afterSingle(Payment $payment): void
    {
        $payment->update(['last_remembered_at' => now()]);
    }

    public function getMailSubject(): string
    {
        return 'Zahlungserinnerung';
    }

    /**
     * @param HasMany<Payment> $query
     *
     * @return HasMany<Payment>
     */
    public static function paymentsQuery(HasMany $query): HasMany
    {
        return $query->whereNeedsRemember();
    }
}
