<?php

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class ContributionMemberApiRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'firstname' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'address' => $this->faker->streetAddress(),
            'zip' => $this->faker->postcode,
            'location' => $this->faker->city(),
            'gender' => $this->faker->randomElement(['Männlich', 'Weiblich']),
            'birthday' => $this->faker->date(),
            'is_leader' => $this->faker->boolean(),
        ];
    }
}
