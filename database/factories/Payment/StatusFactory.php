<?php

namespace Database\Factories\Payment;

use App\Payment\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    public $model = Status::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'is_bill' => $this->faker->boolean,
            'is_remember' => $this->faker->boolean,
        ];
    }
}
