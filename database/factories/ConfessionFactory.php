<?php

namespace Database\Factories;

use App\Confession;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConfessionFactory extends Factory
{
    protected $model = Confession::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(),
            'is_null' => false,
        ];
    }
}
