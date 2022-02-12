<?php

namespace Database\Factories;

use App\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{

    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(5, true),
            'nami_id' => $this->faker->randomNumber(),
        ];
    }
}
