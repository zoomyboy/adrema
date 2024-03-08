<?php

namespace App\Form\Scopes;

use App\Form\Models\Participant;
use App\Lib\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @extends Filter<Participant>
 */
#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class ParticipantFilterScope extends Filter
{
    public function __construct(
        public ?int $parent = null,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function apply(Builder $query): Builder
    {
        if ($this->parent === -1) {
            $query = $query->whereNull('parent_id');
        }

        if (!is_null($this->parent) && $this->parent > 0) {
            $query = $query->where('parent_id', $this->parent);
        }

        return $query;
    }
}
