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
    public Form $form;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public array $data = [],
        public string $search = '',
        public array $options = [],
    ) {
    }

    public function getQuery(): Builder
    {
        $this->search = $this->search ?: '';

        return Participant::search($this->search)->within($this->form->participantsSearchableAs());
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

    /**
     * @inheritdoc
     */
    public function apply(Builder $query): Builder
    {
        foreach ($this->data as $key => $value) {
            if ($value === static::$nan) {
                continue;
            }
            $query = $query->where('data->' . $key, $value);
        }

        return $query;
    }
}
