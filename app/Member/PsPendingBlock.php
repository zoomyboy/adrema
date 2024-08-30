<?php

namespace App\Member;

use App\Dashboard\Blocks\Block;
use Illuminate\Database\Eloquent\Builder;

class PsPendingBlock extends Block
{
    /**
     * @return Builder<Member>
     */
    public function query(): Builder
    {
        return Member::where(function ($query) {
            $time = now()->subYears(5)->endOfYear();

            return $query
                ->orWhere(fn ($query) => $query->whereNull('ps_at')->whereNull('more_ps_at'))
                ->orWhere(fn ($query) => $query->whereNull('ps_at')->where('more_ps_at', '<=', $time))
                ->orWhere(fn ($query) => $query->where('ps_at', '<=', $time)->whereNull('more_ps_at'))
                ->orWhere(fn ($query) => $query->where('ps_at', '>=', $time)->where('more_ps_at', '<=', $time));
        })
            ->whereCurrentGroup()
            ->orderByRaw('lastname, firstname')
            ->whereHas('memberships', fn ($builder) => $builder->isLeader()->active());
    }

    /**
     * @return array{members: array{fullname: string}}
     */
    public function data(): array
    {
        return [
            'members' => $this->query()->get()->map(fn ($member) => [
                'fullname' => $member->fullname,
            ])->toArray(),
        ];
    }

    public function component(): string
    {
        return 'ps-pending';
    }

    public function title(): string
    {
        return 'Ausstehende Präventionsschulungen';
    }
}
