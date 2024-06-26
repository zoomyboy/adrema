<?php

namespace Database\Factories\Fileshare\Models;

use App\Fileshare\ConnectionTypes\ConnectionType;
use App\Fileshare\Models\FileshareConnection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FileshareConnection>
 */
class FileshareConnectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FileshareConnection::class;

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
