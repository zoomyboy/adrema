<?php

namespace Tests\RequestFactories;

use App\Fee;
use Worksome\RequestFactories\RequestFactory;

class SubscriptionRequestFactory extends RequestFactory
{
    /**
     * @return array{fee_id: int, name: string, split: bool, for_promise: bool, children: array<int, array{amount: int, name: string}>}
     */
    public function definition(): array
    {
        return [
            'fee_id' => Fee::factory()->create()->id,
            'name' => $this->faker->words(5, true),
            'split' => $this->faker->boolean(),
            'children' => [],
            'for_promise' => $this->faker->boolean(),
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
            'fee_id' => 9999,
            'name' => '',
            'for_promise' => 'A',
        ]);
    }

    /**
     * @param array<int, Child> $children
     */
    public function children(array $children): self
    {
        return $this->state(['children' => array_map(fn ($child) => $child->toArray(), $children)]);
    }
}
