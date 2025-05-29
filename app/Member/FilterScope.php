<?php

namespace App\Member;

use App\Activity;
use App\Group;
use App\Invoice\BillKind;
use App\Subactivity;
use App\Lib\ScoutFilter;
use Illuminate\Support\Collection;
use Laravel\Scout\Builder;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @extends ScoutFilter<Member>
 */
#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class FilterScope extends ScoutFilter
{

    /**
     * @var array<string, mixed>
     */
    public array $options = [];

    /**
     * @param array<int, int> $activityIds
     * @param array<int, int> $subactivityIds
     * @param array<int, int> $groupIds
     * @param array<int, int> $include
     * @param array<int, int> $exclude
     * @param array<int, array{group_ids: array<int, int>, subactivity_ids: array<int, int>, activity_ids: array<int, int>}> $memberships
     */
    public function __construct(
        public bool $ausstand = false,
        public ?string $billKind = null,
        public array $memberships = [],
        public array $activityIds = [],
        public array $subactivityIds = [],
        public ?string $search = '',
        public array $groupIds = [],
        public array $include = [],
        public array $exclude = [],
        public ?bool $hasFullAddress = null,
        public ?bool $hasBirthday = null,
        public ?bool $hasSvk = null,
        public ?bool $hasVk = null,
    ) {}

    /**
     * @param array<string, mixed> $options
     */
    public function withOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function noPageLimit(): self
    {
        return $this->withOptions([
            'hitsPerPage' => config('scout.meilisearch.index-settings.' . Member::class . '.pagination.maxTotalHits')
        ]);
    }

    public function getQuery(): Builder
    {
        $this->search = $this->search ?: '';

        return Member::search($this->search, function ($engine, string $query, array $options) {
            $filter = collect([]);

            if ($this->hasFullAddress === true) {
                $filter->push('address IS NOT EMPTY');
            }
            if ($this->hasFullAddress === false) {
                $filter->push('address IS EMPTY');
            }
            if ($this->hasBirthday === false) {
                $filter->push('birthday IS NULL');
            }
            if ($this->hasBirthday === true) {
                $filter->push('birthday IS NOT NULL');
            }
            if ($this->hasSvk !== null) {
                $filter->push('has_svk = ' . ($this->hasSvk ? 'true' : 'false'));
            }
            if ($this->hasVk !== null) {
                $filter->push('has_vk = ' . ($this->hasVk ? 'true' : 'false'));
            }
            if ($this->ausstand === true) {
                $filter->push('ausstand > 0');
            }
            if ($this->billKind) {
                $filter->push('bill_kind = ' . BillKind::fromValue($this->billKind)->value);
            }
            if (count($this->groupIds)) {
                $filter->push($this->inExpression('group_id', $this->groupIds));
            }
            if (!$this->subactivityIds && $this->activityIds) {
                $filter->push($this->inExpression('memberships.activity_id', $this->activityIds));
            }
            if ($this->subactivityIds && !$this->activityIds) {
                $filter->push($this->inExpression('memberships.subactivity_id', $this->subactivityIds));
            }
            if ($this->subactivityIds && $this->activityIds) {
                $combinations = $this->combinations($this->activityIds, $this->subactivityIds)
                    ->map(fn($combination) => implode('|', $combination))
                    ->map(fn($combination) => str($combination)->wrap('"'));
                $filter->push($this->inExpression('memberships.both', $combinations));
            }

            foreach ($this->memberships as $membership) {
                $filter->push($this->inExpression('memberships.with_group', $this->possibleValuesForMembership($membership)->map(fn($value) => str($value)->wrap('"'))));
            }

            if (count($this->exclude)) {
                $filter->push($this->notInExpression('id', $this->exclude));
            }

            $andFilter = $filter->map(fn($expression) => "($expression)")->implode(' AND ');

            $options['filter'] = $this->implode(collect([$andFilter])->push($this->inExpression('id', $this->include)), 'OR');
            $options['sort'] = ['lastname:asc', 'firstname:asc'];

            return $engine->search($query, [...$this->options, ...$options]);
        });
    }

    /**
     * @param Collection<int, mixed> $values
     */
    protected function implode(Collection $values, string $between): string
    {
        return $values->filter(fn($expression) => $expression)->implode(" {$between} ");
    }

    /**
     * @param array<int, mixed>|Collection<int, mixed> $values
     */
    private function inExpression(string $key, array|Collection $values): ?string
    {
        if (!count($values)) {
            return null;
        }
        $valueString = Collection::wrap($values)->implode(',');

        return "$key IN [{$valueString}]";
    }

    /**
     * @param array<int, mixed>|Collection<int, mixed> $values
     */
    private function notInExpression(string $key, array|Collection $values): ?string
    {
        if (!count($values)) {
            return null;
        }

        $valueString = Collection::wrap($values)->implode(',');

        return "$key NOT IN [{$valueString}]";
    }

    /**
     * @param array{group_ids: array<int, int>, subactivity_ids: array<int, int>, activity_ids: array<int, int>} $membership
     * @return Collection<int, string>
     */
    protected function possibleValuesForMembership(array $membership): Collection
    {
        $membership['group_ids'] = count($membership['group_ids']) === 0 ? Group::pluck('id')->toArray() : $membership['group_ids'];
        $membership['activity_ids'] = count($membership['activity_ids']) === 0 ? Activity::pluck('id')->toArray() : $membership['activity_ids'];
        $membership['subactivity_ids'] = count($membership['subactivity_ids']) === 0 ? Subactivity::pluck('id')->toArray() : $membership['subactivity_ids'];
        return $this->combinations($membership['group_ids'], $membership['activity_ids'], $membership['subactivity_ids'])
            ->map(fn($combination) => collect($combination)->implode('|'));
    }

    /**
     * @param array<int, array<int, int>> $parts
     * @return Collection<int, array<int, int>>
     */
    protected function combinations(...$parts): Collection
    {
        $firstPart = array_shift($parts);
        $otherParts = $parts;

        if (!count($otherParts)) {
            /** @var Collection<int, Collection<int, int>> */
            return collect($firstPart)->map(fn($p) => [$p]);
        }

        /** @var Collection<int, mixed> */
        $results = collect([]);
        foreach ($firstPart as $firstPartSegment) {
            foreach ($this->combinations(...$otherParts) as $combination) {
                $results->push([$firstPartSegment, ...$combination]);
            }
        }

        return $results;
    }
}
