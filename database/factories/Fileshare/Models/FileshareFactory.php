<?php

namespace Database\Factories\Fileshare\Models;

use App\Fileshare\ConnectionTypes\ConnectionType;
use App\Fileshare\Models\Fileshare;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fileshare>
 */
class FileshareFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Fileshare>
     */
    protected $model = Fileshare::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => '{}',
            'name' => '',
        ];
    }

    public function type(ConnectionType $type): self
    {
        return $this->state(['type' => $type]);
    }

    public function name(string $name): self
    {
        return $this->state(['name' => $name]);
    }
}
