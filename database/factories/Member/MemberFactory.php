<?php

namespace Database\Factories\Member;

use App\Member\Member;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Country;

class MemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'birthday' => $this->faker->dateTimeBetween('-30 years'),
            'joined_at' => $this->faker->dateTimeBetween('-30 years'),
            'send_newspaper' => $this->faker->boolean,
            'address' => $this->faker->streetAddress,
            'zip' => $this->faker->postCode,
            'location' => $this->faker->city,
        ];
    }
}
