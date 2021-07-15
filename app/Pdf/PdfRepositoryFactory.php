<?php

namespace App\Pdf;

use App\Member\Member;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PdfRepositoryFactory
{

    public function fromSingleRequest(string $type, Member $member): ?PdfRepository
    {
        $members = $this->singleMemberCollection($member);

        if ($members->isEmpty()) {
            return null;
        }

        $repo = $this->resolve($type, $members);
        $firstMember = $members->first()->first();

        return $repo->setFilename(
            Str::slug("{$repo->getSubject()} für {$firstMember->firstname} {$firstMember->lastname}"),
        );
    }

    public function singleMemberCollection(Member $member): Collection
    {
        $members = Member::where($member->only(['firstname', 'lastname', 'address', 'zip', 'location']))
            ->whereHas('payments', fn ($q) => $q->whereNeedsPayment())
            ->get();

        return $this->toMemberGroup($members);
    }

    private function resolve(string $kind, Collection $members): PdfRepository
    {
        return new $kind($members);
    }

    private function toMemberGroup(Collection $members): Collection
    {
        return $members->groupBy(
            fn ($member) => Str::slug(
                "{$member->firstname}{$member->lastname}{$member->address}{$member->zip}{$member->location}",
            ),
        );
    }

}
