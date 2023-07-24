<?php

namespace App\Member;

use App\Invoice\BillKind;
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
        return $query->where(function ($query) {
            $query->orWhere(function ($query) {
                if ($this->ausstand) {
                    $query->whereAusstand();
                }

                if ($this->billKind) {
                    $query->where('bill_kind', BillKind::fromValue($this->billKind));
                }

                if (true === $this->hasFullAddress) {
                    $query->whereNotNull('address')->whereNotNull('zip')->whereNotNull('location')->where('address', '!=', '')->where('zip', '!=', '')->where('location', '!=', '');
                }

                if (false === $this->hasFullAddress) {
                    $query->where(fn ($q) => $q
                        ->orWhere('address', '')->orWhereNull('address')
                        ->orWhere('zip', '')->orWhereNull('zip')
                        ->orWhere('location', '')->orWhereNull('location')
                    );
                }

                if (true === $this->hasBirthday) {
                    $query->whereNotNull('birthday');
                }

                if (count($this->groupIds)) {
                    $query->whereIn('group_id', $this->groupIds);
                }

                if (count($this->subactivityIds) + count($this->activityIds) > 0) {
                    $query->whereHas('memberships', function ($q) {
                        $q->active();
                        if (count($this->subactivityIds)) {
                            $q->whereIn('subactivity_id', $this->subactivityIds);
                        }
                        if (count($this->activityIds)) {
                            $q->whereIn('activity_id', $this->activityIds);
                        }
                    });
                }

                if (count($this->exclude)) {
                    $query->whereNotIn('id', $this->exclude);
                }
            })->orWhere(function ($query) {
                if (count($this->include)) {
                    $query->whereIn('id', $this->include);
                }
            });
        });
    }
}
