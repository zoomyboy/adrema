<?php

namespace Database\Factories\Payment;

use App\Fee;
use App\Payment\Subscription;
use App\Payment\SubscriptionChild;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\RequestFactories\Child;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'fee_id' => Fee::factory()->createOne()->id,
        ];
    }

    public function name(string $name): self
    {
        return $this->state(['name' => $name]);
    }

    /**
     * @param array<int, Child> $children
     */
    public function children(array $children): self
    {
        $instance = $this;

        foreach ($children as $child) {
            $instance = $instance->has(SubscriptionChild::factory()->state($child->toArray()), 'children');
        }

        return $instance;
    }
}
