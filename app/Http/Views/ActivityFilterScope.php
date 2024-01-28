<?php

namespace App\Http\Views;

use App\Activity;
use App\Lib\Filter;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Scout\Builder as ScoutBuilder;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @extends Filter<Activity>
 */
#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class ActivityFilterScope extends Filter
{
    public function __construct(
        public ?int $subactivityId = null,
    ) {
    }

    public function getQuery(): ScoutBuilder
    {
        return Activity::search('');
    }

    /**
     * @param Builder<Activity> $query
     *
     * @return Builder<Activity>
     */
    public function apply(Builder $query): Builder
    {
        if ($this->subactivityId) {
            $query->whereHas('subactivities', fn ($query) => $query->where('id', $this->subactivityId));
        }

        return $query;
    }
}
