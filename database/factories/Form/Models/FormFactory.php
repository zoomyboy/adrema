<?php

namespace Database\Factories\Form\Models;

use App\Form\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Feature\Form\FormtemplateSectionRequest;

/**
 * @extends Factory<Form>
 * @method self name(string $name)
 * @method self from(string $from)
 * @method self to(string $to)
 * @method self excerpt(string $excerpt)
 * @method self description(string $description)
 */
class FormFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Form>
     */
    protected $model = Form::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
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
        return $this->state(['config' => ['sections' => $sections]]);
    }

    /**
     * @param mixed $args
     */
    public function __call($method, $parameters): self
    {
        return $this->state([str($method)->snake()->toString() => $parameters[0]]);
    }
}
