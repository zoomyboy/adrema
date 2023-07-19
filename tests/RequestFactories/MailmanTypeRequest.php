<?php

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class MailmanTypeRequest extends RequestFactory
{
    public function definition(): array
    {
        return [
            'url' => 'https://'.$this->faker->domainName(),
            'user' => $this->faker->firstName(),
            'password' => $this->faker->password(),
            'owner' => $this->faker->safeEmail(),
        ];
    }
}
