<?php

namespace Database\Factories;

use App\Group;
use App\Group\Enums\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Group>
 */
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
            'inner_name' => $this->faker->words(5, true),
            'level' => $this->faker->randomElement(Level::cases()),
        ];
    }

    public function inNami(int $namiId): self
    {
        return $this->state(['nami_id' => $namiId]);
    }

    public function name(string $name): self
    {
        return $this->state(['name' => $name]);
    }

    public function level(Level $level): self
    {
        return $this->state(['level' => $level]);
    }

    public function innerName(string $name): self
    {
        return $this->state(['inner_name' => $name]);
    }
}
