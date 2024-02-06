<?php

namespace Tests\Feature\Form;

use App\Form\Fields\Field;
use Worksome\RequestFactories\RequestFactory;

/**
 * @method self name(string $name)
 * @method self key(string $key)
 * @method self required(string|bool $key)
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
        return [
            'name' => $this->faker->words(5, true),
            'key' => str($this->faker->words(5, true))->snake()->toString(),
            'columns' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
            'default' => '',
        ];
    }

    /**
     * @param string|class-string<Field> $field
     */
    public static function type(string $field): self
    {

        if (!$field || !class_exists($field)) {
            return self::new(['type' => $field]);
        }

        return self::new([
            'type' => $field::type(),
            ...$field::fake((new static())->faker),
        ]);
    }

    /**
     * @param mixed $args
     */
    public function __call(string $method, $args): self
    {
        return $this->state([(string) str($method)->snake() => $args[0]]);
    }
}
