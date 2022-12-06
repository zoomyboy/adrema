<?php

namespace App\Letter;

use App\Member\Member;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DocumentFactory
{
    /**
     * @var array<int, class-string<Letter>>
     */
    public array $types = [
        BillDocument::class,
        RememberDocument::class,
    ];

    /**
     * @return Collection<int, Letter>
     */
    public function getTypes(): Collection
    {
        /** @var array<int, Member> */
        $emptyMembers = [];

        return collect(array_map(fn ($classString) => new $classString(collect($emptyMembers)), $this->types));
    }

    /**
     * @param class-string<Letter> $type
     */
    public function fromSingleRequest(string $type, Member $member): ?Letter
    {
        $members = $this->singleMemberPages($member, $type);

        if ($members->isEmpty()) {
            return null;
        }

        return tap(
            $this->resolve($type, $members),
            fn ($repo) => $repo->setFilename(Str::slug("{$repo->getSubject()} für {$members->first()->singleName}"))
        );
    }

    /**
     * @param class-string<Letter> $type
     */
    public function forAll(string $type, BillKind $billKind): ?Letter
    {
        $pages = $this->allMemberPages($type, $billKind);

        if ($pages->isEmpty()) {
            return null;
        }

        return tap($this->resolve($type, $pages), fn ($repo) => $repo->setFilename('alle-rechnungen'));
    }

    /**
     * @param class-string<Letter> $type
     */
    public function afterAll(string $type, BillKind $billKind): void
    {
        $letter = $this->forAll($type, $billKind);
        $this->afterSingle($letter);
    }

    /**
     * @param class-string<Letter> $type
     *
     * @return Collection<int, Letter>
     */
    public function letterCollection(string $type, BillKind $billKind): Collection
    {
        $pages = $this->allMemberPages($type, $billKind);

        return $pages->map(fn ($page) => $this->resolve($type, collect([$page])));
    }

    public function afterSingle(Letter $letter): void
    {
        foreach ($letter->allPayments() as $payment) {
            $letter->afterSingle($payment);
        }
    }

    /**
     * @param class-string<Letter> $type
     *
     * @return Collection<int, Page>
     */
    private function singleMemberPages(Member $member, string $type): Collection
    {
        $members = Member::where($member->only(['lastname', 'address', 'zip', 'location']))
            ->with([
                'payments' => fn ($query) => $type::paymentsQuery($query)
                    ->orderByRaw('nr, member_id'),
            ])
            ->get()
            ->filter(fn (Member $member) => $member->payments->count() > 0);

        return $this->toPages($members);
    }

    /**
     * @param class-string<Letter> $type
     *
     * @return Collection<int, Page>
     */
    private function allMemberPages(string $type, BillKind $billKind): Collection
    {
        $members = Member::where('bill_kind', $billKind)
            ->with([
                'payments' => fn ($query) => $type::paymentsQuery($query)
                    ->orderByRaw('nr, member_id'),
            ])
            ->get()
            ->filter(fn (Member $member) => $member->payments->count() > 0);

        return $this->toPages($members);
    }

    /**
     * @param class-string<Letter>  $type
     * @param Collection<int, Page> $pages
     */
    private function resolve(string $type, Collection $pages): Letter
    {
        return new $type($pages);
    }

    /**
     * @param EloquentCollection<Member> $members
     *
     * @return Collection<int, Page>
     */
    private function toPages(EloquentCollection $members): Collection
    {
        return $members->groupBy(
            fn ($member) => Str::slug(
                "{$member->lastname}{$member->address}{$member->zip}{$member->location}",
            ),
        )->map(fn ($page) => new Page($page));
    }
}
