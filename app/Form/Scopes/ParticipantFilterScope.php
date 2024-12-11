<?php

namespace App\Form\Scopes;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Lib\Filter;
use App\Lib\ScoutFilter;
use Illuminate\Support\Arr;
use Laravel\Scout\Builder;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @extends Filter<Participant>
 */
#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class ParticipantFilterScope extends ScoutFilter
{

    public static string $nan = 'deeb3ef4-d185-44b1-a4bc-0a4e7addebc3d8900c6f-a344-4afb-b54e-065ed483a7ba';
    private Form $form;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public array $data = [],
        public string $search = '',
        public array $options = [],
        public ?int $parent = null
    ) {
    }

    public function getQuery(): Builder
    {
        $this->search = $this->search ?: '';

        return Participant::search($this->search, function ($engine, string $query, array $options) {
            $filter = collect([]);

            foreach ($this->form->getFields()->filterables() as $field) {
                if ($this->data[$field->key] === static::$nan) {
                    continue;
                }
                $filter->push($field->filter($this->data[$field->key]));
            }

            if ($this->parent === -1) {
                $filter->push('parent-id IS NULL');
            }

            if ($this->parent !== null && $this->parent !== -1) {
                $filter->push('parent-id = ' . $this->parent);
            }

            $options['filter'] = $filter->map(fn ($expression) => "($expression)")->implode(' AND ');

            return $engine->search($query, [...$this->options, ...$options]);
        })->within($this->form->participantsSearchableAs());
    }

    public function setForm(Form $form): self
    {
        $this->form = $form;

        foreach ($form->getFields() as $field) {
            if (!Arr::has($this->data, $field->key)) {
                data_set($this->data, $field->key, static::$nan);
            }
        }

        return $this;
    }

    public function parent(?int $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
