<?php

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class InitializeRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'group_id' => (string) $this->faker->numberBetween(100, 200),
            'password' => $this->faker->word(),
            'mglnr' => (string) $this->faker->numberBetween(100, 200),
            'params' => [
                'gruppierung1Id' => $this->faker->numberBetween(100000, 200000),
                'gruppierung2Id' => $this->faker->numberBetween(100000, 200000),
                'gruppierung3Id' => $this->faker->numberBetween(100000, 200000),
                'inGrp' => $this->faker->boolean(),
                'unterhalbGrp' => $this->faker->boolean(),
            ],
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

    /**
     * @param array<string, string|int|bool> $params
     */
    public function withParams(array $params): self
    {
        return $this->state([
            'params' => $params,
        ]);
    }

    public function withGroup(int $group): self
    {
        return $this->state([
            'group_id' => $group,
        ]);
    }
}
