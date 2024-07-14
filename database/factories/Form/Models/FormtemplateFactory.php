<?php

namespace Database\Factories\Form\Models;

use App\Form\Models\Formtemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Feature\Form\FormtemplateSectionRequest;
use Tests\RequestFactories\EditorRequestFactory;

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
            'mail_top' => EditorRequestFactory::new()->toData(),
            'mail_bottom' => EditorRequestFactory::new()->toData(),
            'config' => [
                'sections' => [],
            ],
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

    public function mailTop(EditorRequestFactory $factory): self
    {
        return $this->state(['mail_top' => $factory->toData()]);
    }

    public function mailBottom(EditorRequestFactory $factory): self
    {
        return $this->state(['mail_bottom' => $factory->toData()]);
    }
}
