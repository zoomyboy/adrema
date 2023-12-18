<?php

namespace App\Invoice;

use App\Payment\Payment;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RememberDocument extends InvoiceDocument
{
    public function linkLabel(): string
    {
        return 'Erinnerung erstellen';
    }

    public function getSubject(): string
    {
        return 'Zahlungserinnerung';
    }

    public function view(): string
    {
        return 'tex.remember';
    }

    public static function sendAllLabel(): string
    {
        return 'Erinnerungen versenden';
    }

    public function afterSingle(Payment $payment): void
    {
        $payment->update(['last_remembered_at' => now()]);
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

    /**
     * Get Descriptions for sendpayment page.
     *
     * @return array<int, string>
     */
    public static function getDescription(): array
    {
        return [
            'Diese Funktion erstellt Erinnerungs-PDFs mit allen versendeten aber noch nich bezahlten Rechnungen bei den Mitgliedern die Post als Versandweg haben.',
            'Das zuletzt erinnerte Datum wird auf heute gesetzt.',
        ];
    }
}
