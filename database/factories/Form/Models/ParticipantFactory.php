<?php

namespace Database\Factories\Form\Models;

use App\Form\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Feature\Form\FormtemplateSectionRequest;

/**
 * @extends Factory<Participant>
 */
class ParticipantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Participant>
     */
    protected $model = Participant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'data' => [],
        ];
    }

    /**
     * @param array<int, FormtemplateSectionRequest> $sections
     */
    public function sections(array $sections): self
    {
        return $this->state(['config' => ['sections' => array_map(fn ($section) => $section->create(), $sections)]]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function data(array $data): self
    {
        return $this->state(['data' => $data]);
    }
}
