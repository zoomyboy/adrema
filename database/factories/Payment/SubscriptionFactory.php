<?php

namespace Database\Factories\Payment;

use App\Payment\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{

    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'amount' => $this->faker->numberBetween(1000, 50000),
        ];
    }

}
