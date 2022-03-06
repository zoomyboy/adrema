<?php

namespace Database\Factories;

use App\Subactivity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subactivity>
 */
class SubactivityFactory extends Factory
{

    protected $model = Subactivity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(5, true),
            'nami_id' => $this->faker->randomNumber(),
        ];
    }

    public function inNami(int $namiId): self
    {
        return $this->state(['nami_id' => $namiId]);
    }

}
