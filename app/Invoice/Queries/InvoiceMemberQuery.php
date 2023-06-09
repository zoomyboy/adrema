<?php

namespace App\Invoice\Queries;

use App\Invoice\Invoice;
use App\Invoice\Page;
use App\Member\Member;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class InvoiceMemberQuery
{
    /**
     * @return Builder<Member>
     */
    abstract protected function getQuery(): Builder;

    /**
     * @param class-string<Invoice> $type
     *
     * @return Collection<int, Page>
     */
    public function getPages(string $type): Collection
    {
        return $this->get($type)->groupBy(
            fn ($member) => Str::slug(
                "{$member->lastname}{$member->address}{$member->zip}{$member->location}",
            ),
        )->map(fn ($page) => new Page($page));
    }

    /**
     * @param class-string<Invoice> $type
     *
     * @return EloquentCollection<int, Member>
     */
    private function get(string $type): EloquentCollection
    {
        return $this->getQuery()
            ->with([
                'payments' => fn ($query) => $type::paymentsQuery($query)
                    ->orderByRaw('nr, member_id'),
            ])
            ->get()
            ->filter(fn (Member $member) => $member->payments->count() > 0);
    }
}
