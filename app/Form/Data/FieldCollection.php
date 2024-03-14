<?php

namespace App\Form\Data;

use App\Form\Fields\Field;
use App\Form\Fields\NamiField;
use App\Form\Models\Form;
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

    public function withNamiType(): self
    {
        return $this->filter(fn ($field) => $field->namiType !== null);
    }

    public function noNamiType(): self
    {
        return $this->filter(fn ($field) => $field->namiType === null);
    }

    public function noNamiField(): self
    {
        return $this->filter(fn ($field) => !is_a($field, NamiField::class));
    }

    /**
     * @param array<string, mixed> $input
     */
    public static function fromRequest(Form $form, array $input): self
    {
        return $form->getFields()->each(fn ($field) => $field->value = array_key_exists($field->key, $input) ? $input[$field->key] : $field->default());
    }

    /**
     * @return array<string, mixed>
     */
    public function present(): array
    {
        $attributes = collect([]);

        foreach ($this as $field) {
            $attributes = $attributes->merge($field->present());
        }

        return $attributes->toArray();
    }
}
