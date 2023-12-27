<?php

namespace Tests\Feature\Form;

use App\Form\Fields\Field;
use Worksome\RequestFactories\RequestFactory;

/**
 * @method self name(string $name)
 * @method self type(string $type)
 * @method self key(string $key)
 * @method self required(string|bool $key)
 * @method self type(string $type)
 * @method self rows(int $rows)
 * @method self columns(array{mobile: int, tablet: int, desktop: int} $rows)
 * @method self default(mixed $default)
 */
class FormtemplateFieldRequest extends RequestFactory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(array_column(Field::asMeta(), 'id'));
        return [
            'name' => $this->faker->words(5, true),
            'key' => str($this->faker->words(5, true))->snake()->toString(),
            'type' => $type,
            'columns' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
            'default' => '',
            ...Field::classFromType($type)::fake($this->faker),
        ];
    }

    /**
     * @param string|class-string<Field> $field
     */
    public function type(string $field): self
    {
        if (!$field || !class_exists($field)) {
            return $this->state(['type' => $field]);
        }

        return $this->state([
            'type' => $field::type(),
            ...$field::fake($this->faker),
        ]);
    }

    /**
     * @param mixed $args
     */
    public function __call(string $method, $args): self
    {
        return $this->state([$method => $args[0]]);
    }
}
