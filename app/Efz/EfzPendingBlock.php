<?php

namespace App\Efz;

use App\Dashboard\Blocks\Block;
use App\Member\Member;
use Illuminate\Database\Eloquent\Builder;

class EfzPendingBlock extends Block
{
    /**
     * @return Builder<Member>
     */
    public function query(): Builder
    {
        return Member::where(function ($query) {
            return $query->where('efz', '<=', now()->subYears(5)->endOfYear())
                ->orWhereNull('efz');
        })
            ->whereCurrentGroup()
            ->orderByRaw('lastname, firstname')
            ->whereHas('memberships', fn ($builder) => $builder->isLeader());
    }

    /**
     * @return array{members: array<int, string>}
     */
    public function data(): array
    {
        return [
            'members' => $this->query()->get()->map(fn ($member) => $member->fullname)->toArray(),
        ];
    }

    public function component(): string
    {
        return 'efz-pending';
    }

    public function title(): string
    {
        return 'Ausstehende Führungszeugnisse';
    }
}
