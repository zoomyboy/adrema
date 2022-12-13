<?php

namespace Database\Factories\Payment;

use App\Payment\SubscriptionChild;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<SubscriptionChild>
 */
class SubscriptionChildFactory extends Factory
{
    protected $model = SubscriptionChild::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'amount' => $this->faker->numberBetween(10, 3000),
            'name' => $this->faker->words(5, true),
        ];
    }
}
