<?php

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class MailmanListRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'description' => $this->faker->words(5, true),
            'display_name' => $this->faker->words(5, true),
            'fqdn_listname' => $this->faker->safeEmail,
            'http_etag' => $this->faker->uuid(),
            'list_id' => str_replace('@', '.', $this->faker->safeEmail()),
            'list_name' => $this->faker->words(1, true),
            'mail_host' => $this->faker->safeEmailDomain(),
            'member_count' => $this->faker->numberBetween(0, 100),
            'self_link' => $this->faker->url(),
            'volume' => 1,
        ];
    }
}
