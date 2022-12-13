<?php

namespace Database\Factories\Payment;

use App\Fee;
use App\Payment\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'amount' => $this->faker->numberBetween(1000, 50000),
            'fee_id' => Fee::factory()->createOne()->id,
        ];
    }

    public function name(string $name): self
    {
        return $this->state(['name' => $name]);
    }

    public function amount(int $amount): self
    {
        return $this->state(['amount' => $amount]);
    }
}
