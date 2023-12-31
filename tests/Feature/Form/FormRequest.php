<?php

namespace Tests\Feature\Form;

use Worksome\RequestFactories\RequestFactory;

/**
 * @method self name(string $name)
 * @method self description(string $description)
 * @method self excerpt(string $description)
 */
class FormRequest extends RequestFactory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(4, true),
            'description' => $this->faker->text(),
            'excerpt' => $this->faker->words(10, true),
            'config' => ['sections' => []],
            'from' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'to' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'registration_from' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'registration_until' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'mail_top' => $this->faker->text(),
            'mail_bottom' => $this->faker->text(),
        ];
    }

    /**
     * @param array<int, FormtemplateSectionRequest> $sections
     */
    public function sections(array $sections): self
    {
        return $this->state(['config.sections' => $sections]);
    }

    /**
     * @param mixed $args
     */
    public function __call(string $method, $args): self
    {
        return $this->state([str($method)->snake()->toString() => $args[0]]);
    }
}
