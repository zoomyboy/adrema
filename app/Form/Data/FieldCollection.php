<?php

namespace App\Form\Data;

use App\Form\Contracts\Filterable;
use App\Form\Enums\SpecialType;
use App\Form\Fields\Field;
use App\Form\Fields\NamiField;
use App\Form\Models\Form;
use Illuminate\Support\Collection;
use stdClass;

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

    public function hasNamiField(): bool
    {
        return $this->first(fn ($field) => is_a($field, NamiField::class)) !== null;
    }

    /**
     * @return stdClass
     */
    public function getMailRecipient(): ?stdClass
    {
        $email = $this->findBySpecialType(SpecialType::EMAIL)?->value;

        return $this->getFullname() && $email
            ? (object) [
                'name' => $this->getFullname(),
                "email" => $email,
            ] : null;
    }

    public function getFullname(): ?string
    {
        $firstname = $this->findBySpecialType(SpecialType::FIRSTNAME)?->value;
        $lastname = $this->findBySpecialType(SpecialType::LASTNAME)?->value;

        return $firstname && $lastname ? "$firstname $lastname" : null;
    }

    /**
     * @param array<string, mixed> $input
     */
    public static function fromRequest(Form $form, array $input): self
    {
        return $form->getFields()->map(function ($field) use ($input) {
            $field->value = array_key_exists($field->key, $input) ? $input[$field->key] : $field->default();
            return $field;
        });
    }

    public function find(Field $givenField): ?Field
    {
        return $this->findByKey($givenField->key);
    }

    public function findByKey(string $key): ?Field
    {
        return $this->first(fn ($field) => $field->key === $key);
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

    /**
     * @return array<int, string>
     */
    public function names(): array
    {
        return $this->map(fn ($field) => $field->name)->toArray();
    }

    /**
     * @return array<int, string>
     */
    public function presentValues(): array
    {
        return $this->map(fn ($field) => $field->presentRaw())->toArray();
    }

    private function findBySpecialType(SpecialType $specialType): ?Field
    {
        return $this->first(fn ($field) => $field->specialType === $specialType);
    }

    public function searchables(): self
    {
        return $this;
    }

    public function filterables(): self
    {
        return $this->filter(fn ($field) => $field instanceof Filterable);
    }

    /**
     * @return array<int, string>
     */
    public function getKeys(): array
    {
        return $this->map(fn ($field) => $field->key)->toArray();
    }
}
