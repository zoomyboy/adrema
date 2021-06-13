<?php

namespace Database\Factories;

use App\Nationality;
use Illuminate\Database\Eloquent\Factories\Factory;

class NationalityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Nationality::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->country.'Nationality',
            'nami_id' => $this->faker->randomNumber(),
        ];
    }
}
