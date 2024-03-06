<?php

namespace App\Form\Data;

use App\Form\Fields\Field;
use App\Form\Fields\NamiField;
use Illuminate\Support\Collection;

/**
 * @extends Collection<int, Field>
 */
class FieldCollection extends Collection
{

    public function forMembers(): self
    {
        return $this->filter(fn ($field) => $field->forMembers === true);
    }

    public function noNamiType(): self
    {
        return $this->filter(fn ($field) => $field->namiType === null);
    }

    public function noNamiField(): self
    {
        return $this->filter(fn ($field) => !is_a($field, NamiField::class));
    }
}
