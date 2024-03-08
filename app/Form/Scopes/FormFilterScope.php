<?php

namespace App\Form\Scopes;

use Laravel\Scout\Builder;
use App\Form\Models\Form;
use App\Lib\ScoutFilter;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @extends ScoutFilter<Form>
 */
#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class FormFilterScope extends ScoutFilter
{
    public function __construct(
        public ?string $search = '',
        public bool $past = false,
    ) {
    }

    public function getQuery(): Builder
    {
        $this->search = $this->search ?: '';

        return Form::search($this->search, function ($engine, string $query, array $options) {
            $options['sort'] = ['from:asc'];

            $filters = collect([]);

            if ($this->past === false) {
                $filters->push('to > ' . now()->timestamp);
            }

            $options['filter'] = $filters->implode(' AND ');

            return $engine->search($query, $options);
        });
    }
}
