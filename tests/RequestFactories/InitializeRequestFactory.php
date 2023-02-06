<?php

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class InitializeRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'group_id' => $this->faker->numberBetween(100, 200),
            'password' => $this->faker->word(),
            'mglnr' => $this->faker->numberBetween(100, 200),
        ];
    }

    public function invalid(): self
    {
        return $this->state([
            'group_id' => null,
            'password' => null,
            'mglnr' => null,
        ]);
    }

    public function withCredentials(int $mglnr, string $password): self
    {
        return $this->state([
            'mglnr' => $mglnr,
            'password' => $password,
        ]);
    }

    public function withGroup(int $group): self
    {
        return $this->state([
            'group_id' => $group,
        ]);
    }
}
