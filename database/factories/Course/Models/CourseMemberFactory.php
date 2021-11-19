<?php

namespace Database\Factories\Course\Models;

use App\Course\Models\CourseMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseMemberFactory extends Factory
{

    public $model = CourseMember::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'organizer' => $this->faker->words(5, true),
            'event_name' => $this->faker->words(5, true),
            'nami_id' => $this->faker->numberBetween(1111, 9999),
            'completed_at' => $this->faker->date(),
        ];
    }

    public function inNami(int $namiId): self
    {
        return $this->state(['nami_id' => $namiId]);
    }
}
