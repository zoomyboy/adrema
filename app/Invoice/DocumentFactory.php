<?php

namespace App\Invoice;

use App\Invoice\Queries\InvoiceMemberQuery;
use Illuminate\Support\Collection;

class DocumentFactory
{
    /**
     * @var array<int, class-string<Invoice>>
     */
    private array $types = [
        BillDocument::class,
        RememberDocument::class,
    ];

    /**
     * @return Collection<int, class-string<Invoice>>
     */
    public function getTypes(): Collection
    {
        return collect($this->types);
    }

    /**
     * @param class-string<Invoice> $type
     */
    public function singleInvoice(string $type, InvoiceMemberQuery $query): ?Invoice
    {
        $pages = $query->getPages($type);

        if ($pages->isEmpty()) {
            return null;
        }

        return $this->resolve($type, $pages);
    }

    /**
     * @param class-string<Invoice> $type
     *
     * @return Collection<int, Invoice>
     */
    public function invoiceCollection(string $type, InvoiceMemberQuery $query): Collection
    {
        return $query
            ->getPages($type)
            ->map(fn ($page) => $this->resolve($type, collect([$page])));
    }

    public function afterSingle(Invoice $invoice): void
    {
        foreach ($invoice->allPayments() as $payment) {
            $invoice->afterSingle($payment);
        }
    }

    /**
     * @param class-string<Invoice> $type
     * @param Collection<int, Page> $pages
     */
    private function resolve(string $type, Collection $pages): Invoice
    {
        return new $type($pages);
    }
}
