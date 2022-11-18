<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Region>
 */
class RegionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'nami_id' => $this->faker->numberBetween(100, 200),
            'is_null' => false,
        ];
    }

    public function name(string $name): self
    {
        return $this->state(['name' => $name]);
    }

    public function inNami(int $namiId): self
    {
        return $this->state(['nami_id' => $namiId]);
    }
}
