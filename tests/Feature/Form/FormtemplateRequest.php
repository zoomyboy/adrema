<?php

namespace Tests\Feature\Form;

use Worksome\RequestFactories\RequestFactory;

/**
 * @method self name(string $name)
 */
class FormtemplateRequest extends RequestFactory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(5, true),
            'config' => ['sections' => []],
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
        return $this->state([$method => $args[0]]);
    }
}
