<?php

namespace App\Membership;

use App\Home\Blocks\Block;
use App\Member\Membership;
use Illuminate\Database\Eloquent\Builder;

class AgeGroupCountBlock extends Block
{
    /**
     * @return Builder<Membership>
     */
    public function query(): Builder
    {
        return Membership::select('subactivities.slug', 'subactivities.name')
            ->selectRaw('COUNT(member_id) AS count')
            ->join('activities', 'memberships.activity_id', 'activities.id')
            ->join('subactivities', 'memberships.subactivity_id', 'subactivities.id')
            ->isAgeGroup()
            ->isMember()
            ->groupBy('subactivities.slug', 'subactivities.name')
            ->orderBy('subactivity_id');
    }

    protected function data(): array
    {
        return [
            'groups' => $this->query()->get()->toArray(),
        ];
    }

    public function component(): string
    {
        return 'age-group-count';
    }

    public function title(): string
    {
        return 'Gruppierungs-Verteilung';
    }
}
