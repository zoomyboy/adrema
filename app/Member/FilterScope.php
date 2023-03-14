<?php

namespace App\Member;

use App\Letter\BillKind;
use App\Lib\Filter;
use Illuminate\Database\Eloquent\Builder;
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
    public function __construct(
        public bool $ausstand = false,
        public ?string $billKind = null,
        public ?int $activityId = null,
        public ?int $subactivityId = null,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function locks(): array
    {
        return [];
    }

    /**
     * @param Builder<Member> $query
     *
     * @return Builder<Member>
     */
    public function apply(Builder $query): Builder
    {
        if ($this->ausstand) {
            $query->whereAusstand();
        }

        if ($this->billKind) {
            $query->where('bill_kind', BillKind::fromValue($this->billKind));
        }
        if ($this->subactivityId || $this->activityId) {
            $query->whereHas('memberships', function ($q) {
                $q->active();
                if ($this->subactivityId) {
                    $q->where('subactivity_id', $this->subactivityId);
                }
                if ($this->activityId) {
                    $q->where('activity_id', $this->activityId);
                }
            });
        }

        return $query;
    }
}
