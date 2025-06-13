<?php

namespace App\Membership;

use App\Lib\Filter;
use App\Member\Membership;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Filter<Membership>
 */
class FilterScope extends Filter
{
    /**
     * @param array<int, int> $activities
     * @param array<int, int> $subactivities
     * @param array<int, int> $groups
     */
    public function __construct(
        public array $activities = [],
        public array $subactivities = [],
        public array $groups = [],
        public ?bool $active = true,
    ) {}

    public function getQuery(): Builder
    {
        $query = (new Membership())->newQuery();

        if ($this->active === true) {
            $query = $query->active();
        }

        if ($this->active === false) {
            $query = $query->inactive();
        }

        if (count($this->groups)) {
            $query = $query->whereIn('group_id', $this->groups);
        }

        if (count($this->activities)) {
            $query = $query->whereIn('activity_id', $this->activities);
        }

        if (count($this->subactivities)) {
            $query = $query->whereIn('subactivity_id', $this->subactivities);
        }

        return $query;
    }
}
