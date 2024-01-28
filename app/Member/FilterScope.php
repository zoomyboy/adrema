<?php

namespace App\Member;

use App\Invoice\BillKind;
use App\Lib\Filter;
use Illuminate\Support\Collection;
use Laravel\Scout\Builder;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @extends Filter<Member>
 */
#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class FilterScope extends Filter
{
    /**
     * @param array<int, int> $activityIds
     * @param array<int, int> $subactivityIds
     * @param array<int, int> $groupIds
     * @param array<int, int> $include
     * @param array<int, int> $exclude
     */
    public function __construct(
        public bool $ausstand = false,
        public ?string $billKind = null,
        public array $activityIds = [],
        public array $subactivityIds = [],
        public ?string $search = '',
        public array $groupIds = [],
        public array $include = [],
        public array $exclude = [],
        public ?bool $hasFullAddress = null,
        public ?bool $hasBirthday = null,
    ) {
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
                $combinations = collect($this->activityIds)
                    ->map(fn ($activityId) => collect($this->subactivityIds)->map(fn ($subactivityId) => $activityId . '|' . $subactivityId))
                    ->flatten()
                    ->map(fn ($combination) => str($combination)->wrap('"'));
                $filter->push($this->inExpression('memberships.both', $combinations));
            }

            if (count($this->exclude)) {
                $filter->push($this->notInExpression('id', $this->exclude));
            }

            $andFilter = $filter->map(fn ($expression) => "($expression)")->implode(' AND ');

            $options['filter'] = $this->implode(collect([$andFilter])->push($this->inExpression('id', $this->include)), 'OR');
            $options['sort'] = ['lastname:asc', 'firstname:asc'];

            return $engine->search($query, $options);
        });
    }

    /**
     * @param Collection<int, mixed> $values
     */
    protected function implode(Collection $values, string $between): string
    {
        return $values->filter(fn ($expression) => $expression)->implode(" {$between} ");
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
}
