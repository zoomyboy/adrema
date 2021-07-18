<?php

namespace App\Pdf;

use App\Member\Member;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PdfRepositoryFactory
{

    private array $types = [
        BillType::class,
    ];

    public function getTypes(): Collection
    {
        return collect($this->types);
    }

    public function fromSingleRequest(string $type, Member $member): ?PdfRepository
    {
        $members = $this->singleMemberCollection($member, $type);

        if ($members->isEmpty()) {
            return null;
        }

        $repo = $this->resolve($type, $members);
        $firstMember = $members->first()->first();

        return $repo->setFilename(
            Str::slug("{$repo->getSubject()} für {$firstMember->lastname}"),
        );
    }

    public function forAll(string $type, string $billKind): ?PdfRepository
    {
        $members = $this->toMemberGroup($this->allMemberCollection($type, $billKind));

        if ($members->isEmpty()) {
            return null;
        }

        return $this->resolve($type, $members)->setFilename('alle-rechnungen');
    }

    public function repoCollection(string $type, string $billKind): Collection
    {
        $members = $this->toMemberGroup($this->allMemberCollection($type, $billKind));

        return $members->map(function (Collection $members) use ($type) {
            $repo = $this->resolve($type, collect([$members]));

            return $repo->setFilename(Str::slug("{$repo->getSubject()} für {$members->first()->lastname}"));
        });
    }

    public function afterSingle(PdfRepository $repo): void
    {
        foreach ($repo->allPayments() as $payment) {
            $payment->update(['status_id' => 2]);
        }
    }

    public function afterAll(string $type, string $billKind): void
    {
        $members = $this->allMemberCollection($type, $billKind);
        $repo = $this->resolve($type, $this->toMemberGroup($members));

        $this->afterSingle($repo);
    }

    public function singleMemberCollection(Member $member, string $type): Collection
    {
        $members = Member::where($member->only(['lastname', 'address', 'zip', 'location']))
            ->get()
            ->filter(fn (Member $member) => app($type)->createable($member));

        return $this->toMemberGroup($members);
    }

    private function allMemberCollection(string $type, string $billKind): Collection
    {
        return Member::whereHas('billKind', fn (Builder $q) => $q->where('name', $billKind))
            ->get()
            ->filter(fn (Member $member) => app($type)->createable($member));
    }

    private function resolve(string $kind, Collection $members): PdfRepository
    {
        return new $kind($members);
    }

    private function toMemberGroup(Collection $members): Collection
    {
        return $members->groupBy(
            fn ($member) => Str::slug(
                "{$member->lastname}{$member->address}{$member->zip}{$member->location}",
            ),
        );
    }

}
