<?php

namespace App\Pdf;

use App\Member\Member;
use App\Payment\Payment;
use Illuminate\Support\Collection;

class RememberType extends Repository implements PdfRepository
{

    public string $filename;
    public Collection $pages;

    public function __construct(Collection $pages)
    {
        $this->pages = $pages;
    }

    public function getPayments(Member $member): Collection
    {
        return $member->payments()->whereNeedsRemember()->get();
    }

    public function linkLabel(): string
    {
        return 'Erinnerung erstellen';
    }

    public function getSubject(): string
    {
        return 'Zahlungserinnerung';
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getView(): string
    {
        return 'tex.remember';
    }

    public function getTemplate(): string
    {
        return 'default';
    }

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

    public function getFamilyName(Collection $page): string
    {
        return $page->first()->lastname;
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

    public function getUsage(Collection $page): string
    {
        return "Mitgliedsbeitrag für {$this->getFamilyName($page)}";
    }

    public function allLabel(): string 
    {
        return 'Erinnerungen versenden';
    }

    /**
     * Get Descriptions for sendpayment page
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

}
