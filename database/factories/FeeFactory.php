<?php

namespace Database\Factories;

use App\Fee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fee>
 */
class FeeFactory extends Factory
{
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
