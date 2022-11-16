<?php

namespace Database\Factories;

use App\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    /** @var array<int, string> */
    private array $tries = [
        'Schnuppermitgliedschaft',
    ];

    /** @var array<int, string> */
    private array $members = [
        '€ Mitglied',
        'Schnuppermitgliedschaft',
    ];

    /** @var array<int, string> */
    private array $filterableActivities = [
        '€ Mitglied',
        '€ passive Mitgliedschaft',
        '€ KassiererIn',
        '€ LeiterIn',
        'Schnuppermitgliedschaft',
    ];

    /** @var array<int, string> */
    private array $efz = [
        '€ LeiterIn',
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

    public function name(string $name): self
    {
        return $this->state([
            'name' => $name,
            'is_try' => in_array($name, $this->tries),
            'is_member' => in_array($name, $this->members),
            'is_filterable' => in_array($name, $this->filterableActivities),
            'has_efz' => in_array($name, $this->efz),
        ]);
    }
}
