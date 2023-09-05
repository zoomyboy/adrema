<?php

namespace App\Membership;

use App\Dashboard\Blocks\Block;
use App\Member\Member;
use Illuminate\Database\Eloquent\Builder;

class TestersBlock extends Block
{
    /**
     * @return Builder<Member>
     */
    public function query(): Builder
    {
        return Member::whereHas('memberships', fn ($q) => $q->isTrying())
            ->with('memberships', fn ($q) => $q->isTrying());
    }

    /**
     * @return array{members: array<int, array{name: string, try_ends_at: string, try_ends_at_human: string}>}
     */
    public function data(): array
    {
        return [
            'members' => $this->query()->get()->map(fn ($member) => [
                'name' => $member->fullname,
                'try_ends_at' => $member->memberships->first()->from->addWeeks(8)->format('d.m.Y'),
                'try_ends_at_human' => $member->memberships->first()->from->addWeeks(8)->diffForHumans(),
            ])->toArray(),
        ];
    }

    public function component(): string
    {
        return 'testers';
    }

    public function title(): string
    {
        return 'Endende Schhnupperzeiten';
    }
}
