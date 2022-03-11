<?php

namespace App\Pdf;

use App\Member\Member;
use App\Payment\Payment;
use Illuminate\Support\Collection;

class BillType extends Repository implements PdfRepository
{
    public string $filename;

    public function getPayments(Member $member): Collection
    {
        return $member->payments()->whereNeedsBill()->get();
    }

    public function linkLabel(): string
    {
        return 'Rechnung erstellen';
    }

    public function getSubject(): string
    {
        return 'Rechnung';
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
        return 'tex.bill';
    }

    public function getTemplate(): string
    {
        return 'default';
    }

    public function getPositions(Collection $page): array
    {
        $memberIds = $page->pluck('id')->toArray();
        $payments = Payment::whereIn('member_id', $memberIds)
            ->orderByRaw('nr, member_id')->whereNeedsBill()->get();

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
        return 'Rechnungen versenden';
    }

    /**
     * Get Descriptions for sendpayment page.
     *
     * @return array<int, string>
     */
    public function getDescription(): array
    {
        return [
            'Diese Funktion erstellt ein PDF mit allen noch nicht versendenden Rechnungen bei den Mitgliedern die Post als Versandweg haben.',
            'Die Rechnungen werden automatisch auf "Rechnung gestellt" aktualisiert.',
        ];
    }

    public function afterSingle(Payment $payment): void
    {
        $payment->update(['status_id' => 2]);
    }

    public function getMailSubject(): string
    {
        return 'Jahresrechnung';
    }
}
