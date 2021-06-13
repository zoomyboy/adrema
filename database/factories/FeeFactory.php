<?php

namespace Database\Factories;

use App\Fee;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Fee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Normaler Beitrag', 'Familienermäßigt']),
            'nami_id' => $this->faker->randomNumber(),
        ];
    }
}
