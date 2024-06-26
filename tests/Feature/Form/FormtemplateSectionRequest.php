<?php

namespace Tests\Feature\Form;

use Worksome\RequestFactories\RequestFactory;

/**
 * @method self name(string $name)
 */
class FormtemplateSectionRequest extends RequestFactory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(5, true),
            'intro' => null,
            'fields' => [],
        ];
    }

    /**
     * @param array<int, FormtemplateFieldRequest> $fields
     */
    public function fields(array $fields): self
    {
        return $this->state(['fields' => $fields]);
    }

    public function intro(string $intro): self
    {
        return $this->state(['intro' => $intro]);
    }

    /**
     * @param mixed $args
     */
    public function __call(string $method, $args): self
    {
        return $this->state([$method => $args[0]]);
    }
}
