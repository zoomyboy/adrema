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

    /** @var array<int, string> */
    private array $filterableSubactivities = [
        'Biber',
        'Wölfling',
        'Jungpfadfinder',
        'Pfadfinder',
        'Vorstand',
        'Rover',
    ];

    /** @var array<int, string> */
    private array $ageGroups = [
        'Biber',
        'Wölfling',
        'Jungpfadfinder',
        'Pfadfinder',
        'Rover',
    ];

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

    public function ageGroup(): self
    {
        return $this->state(['is_age_group' => true]);
    }

    public function filterable(): self
    {
        return $this->state(['is_filterable' => true]);
    }

    public function name(string $name): self
    {
        return $this->state([
            'name' => $name,
            'is_filterable' => in_array($name, $this->filterableSubactivities),
            'is_age_group' => in_array($name, $this->ageGroups),
        ]);
    }
}
