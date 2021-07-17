<?php

namespace App\Pdf;

use App\Member\Member;
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

    public function singleMemberCollection(Member $member, string $type): Collection
    {
        $members = Member::where($member->only(['lastname', 'address', 'zip', 'location']))
            ->get()
            ->filter(fn (Member $member) => app($type)->createable($member));

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
                "{$member->lastname}{$member->address}{$member->zip}{$member->location}",
            ),
        );
    }

}
