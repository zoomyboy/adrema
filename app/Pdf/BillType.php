<?php

namespace App\Pdf;

use App\Payment\Payment;
use Illuminate\Support\Collection;

class BillType extends Repository implements PdfRepository
{

    public string $filename;
    public Collection $pages;

    public function __construct(Collection $pages)
    {
        $this->pages = $pages;
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
        $payments = Payment::whereIn('member_id', $memberIds)->whereNeedsBill()->get();

        return $payments->mapWithKeys(function (Payment $payment) {
            $key = "Beitrag für {$payment->nr} ({$payment->subscription->name})";

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

    public function getLocation(Collection $page): string
    {
        return $page->first()->location;
    }

}
