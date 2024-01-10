<?php

namespace Database\Factories\Form\Models;

use App\Form\Models\Formtemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Feature\Form\FormtemplateSectionRequest;

/**
 * @extends Factory<Formtemplate>
 * @method self name(string $name)
 */
class FormtemplateFactory extends Factory
{

    public $model = Formtemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(4, true),
            'config' => [],
        ];
    }

    /**
     * @param array<int, FormtemplateSectionRequest> $sections
     */
    public function sections(array $sections): self
    {
        return $this->state(['config' => ['sections' => array_map(fn ($section) => $section->create(), $sections)]]);
    }

    /**
     * @param mixed $parameters
     */
    public function __call($method, $parameters): self
    {
        return $this->state([str($method)->snake()->toString() => $parameters[0]]);
    }
}
