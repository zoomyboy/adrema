<?php

namespace Database\Factories;

use App\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gender>
 */
class GenderFactory extends Factory
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
        ];
    }

    public function name(string $name): self
    {
        return $this->state(['name' => $name]);
    }

    public function male(): self
    {
        return $this->name('Männlich');
    }

    public function female(): self
    {
        return $this->name('Weiblich');
    }

    public function inNami(int $namiId): self
    {
        return $this->state(['nami_id' => $namiId]);
    }
}
