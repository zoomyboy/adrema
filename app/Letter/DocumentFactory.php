<?php

namespace App\Letter;

use App\Member\Member;
use Illuminate\Database\Eloquent\Builder;
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
        $members = $this->singleMemberCollection($member, $type);

        if ($members->isEmpty()) {
            return null;
        }

        $repo = $this->resolve($type, $members);
        $repo->setFilename(Str::slug("{$repo->getSubject()} für {$members->first()->singleName}"));

        return $repo;
    }

    /**
     * @param class-string<Letter> $type
     */
    public function forAll(string $type, string $billKind): ?Letter
    {
        $members = $this->toPages($this->allMemberCollection($type, $billKind));

        if ($members->isEmpty()) {
            return null;
        }

        return $this->resolve($type, $members)->setFilename('alle-rechnungen');
    }

    /**
     * @param class-string<Letter> $type
     *
     * @return Collection<int, Letter>
     */
    public function repoCollection(string $type, string $billKind): Collection
    {
        $pages = $this->toPages($this->allMemberCollection($type, $billKind));

        return $pages->map(fn ($page) => $this->resolve($type, collect([$page])));
    }

    public function afterSingle(Letter $repo): void
    {
        foreach ($repo->allPayments() as $payment) {
            $repo->afterSingle($payment);
        }
    }

    /**
     * @param class-string<Letter> $type
     */
    public function afterAll(string $type, string $billKind): void
    {
        $members = $this->allMemberCollection($type, $billKind);
        $repo = $this->resolve($type, $this->toPages($members));

        $this->afterSingle($repo);
    }

    /**
     * @param class-string<Letter> $type
     *
     * @return Collection<int, Page>
     */
    public function singleMemberCollection(Member $member, string $type): Collection
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
     * @return EloquentCollection<Member>
     */
    private function allMemberCollection(string $type, string $billKind): Collection
    {
        return Member::whereHas('billKind', fn (Builder $q) => $q->where('name', $billKind))
            ->with([
                'payments' => fn ($query) => $type::paymentsQuery($query)
                    ->orderByRaw('nr, member_id'),
            ])
            ->get()
            ->filter(fn (Member $member) => $member->payments->count() > 0);
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
