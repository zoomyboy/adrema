<?php

namespace Database\Factories\Form\Models;

use App\Form\Models\Formtemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Formtemplate>
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
}
