<?php

namespace App\Home\Queries;

use App\Member\Membership;
use Illuminate\Database\Eloquent\Builder;

class GroupQuery
{
    /**
     * @var Builder<Membership>
     */
    private Builder $query;

    public function execute(): self
    {
        $this->query = Membership::select('subactivities.slug', 'subactivities.name')
            ->selectRaw('COUNT(member_id) AS count')
            ->join('activities', 'memberships.activity_id', 'activities.id')
            ->join('subactivities', 'memberships.subactivity_id', 'subactivities.id')
            ->isAgeGroup()
            ->isMember()
            ->groupBy('subactivities.slug', 'subactivities.name')
            ->orderBy('subactivity_id');

        return $this;
    }

    /**
     * @return array<int, array{slug: string, name: string, count: int}>
     */
    public function getResult(): array
    {
        return $this->query->get()->toArray();
    }
}
