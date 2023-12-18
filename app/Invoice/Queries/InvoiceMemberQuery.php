<?php

namespace App\Invoice\Queries;

use App\Invoice\InvoiceDocument;
use App\Member\Member;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class InvoiceMemberQuery
{
    /**
     * @param class-string<InvoiceDocument> $type
     */
    public string $type;

    /**
     * @return Builder<Member>
     */
    abstract protected function getQuery(): Builder;

    /**
     * @return Collection<(int|string), EloquentCollection<(int|string), Member>>
     */
    public function getMembers(): Collection
    {
        return $this->get()->groupBy(
            fn ($member) => Str::slug(
                "{$member->lastname}{$member->address}{$member->zip}{$member->location}",
            ),
        )->toBase();
    }

    /**
     * @param class-string<InvoiceDocument> $type
     */
    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return EloquentCollection<int, Member>
     */
    private function get(): EloquentCollection
    {
        return $this->getQuery()
            ->with([
                'payments' => fn ($query) => $this->type::paymentsQuery($query)
                    ->orderByRaw('nr, member_id'),
            ])
            ->get()
            ->filter(fn (Member $member) => $member->payments->count() > 0);
    }
}
