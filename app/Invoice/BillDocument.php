<?php

namespace App\Invoice;

use App\Payment\Payment;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillDocument extends Invoice
{
    public function linkLabel(): string
    {
        return 'Rechnung erstellen';
    }

    public function getSubject(): string
    {
        return 'Rechnung';
    }

    public function view(): string
    {
        return 'tex.bill';
    }

    public static function sendAllLabel(): string
    {
        return 'Rechnungen versenden';
    }

    public function afterSingle(Payment $payment): void
    {
        $payment->update(['status_id' => 2]);
    }

    public function getMailSubject(): string
    {
        return 'Jahresrechnung';
    }

    /**
     * @param HasMany<Payment> $query
     *
     * @return HasMany<Payment>
     */
    public static function paymentsQuery(HasMany $query): HasMany
    {
        return $query->whereNeedsBill();
    }

    /**
     * Get Descriptions for sendpayment page.
     *
     * @return array<int, string>
     */
    public static function getDescription(): array
    {
        return [
            'Diese Funktion erstellt ein PDF mit allen noch nicht versendenden Rechnungen bei den Mitgliedern die Post als Versandweg haben.',
            'Die Rechnungen werden automatisch auf "Rechnung gestellt" aktualisiert.',
        ];
    }
}
