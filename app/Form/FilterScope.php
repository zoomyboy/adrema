<?php

namespace App\Form;

use App\Form\Models\Form;
use App\Lib\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @extends Filter<Form>
 */
#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class FilterScope extends Filter
{
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function locks(): array
    {
        return [];
    }

    /**
     * @param Builder<Form> $query
     *
     * @return Builder<Form>
     */
    public function apply(Builder $query): Builder
    {
        return $query;
    }
}
