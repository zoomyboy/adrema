<?php

namespace Tests\RequestFactories;

use App\Fee;
use Worksome\RequestFactories\RequestFactory;

class SubscriptionRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(100, 2000),
            'fee_id' => Fee::factory()->create()->id,
            'name' => $this->faker->words(5, true),
        ];
    }

    public function amount(int $amount): self
    {
        return $this->state(['amount' => $amount]);
    }

    public function fee(Fee $fee): self
    {
        return $this->state(['fee_id' => $fee->id]);
    }

    public function name(string $name): self
    {
        return $this->state(['name' => $name]);
    }

    public function invalid(): self
    {
        return $this->state([
            'amount' => '',
            'fee_id' => 9999,
            'name' => '',
        ]);
    }
}
