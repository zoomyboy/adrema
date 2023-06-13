<?php

namespace Tests\RequestFactories;

use App\Mailman\Data\MailingList;
use Worksome\RequestFactories\RequestFactory;

class MailinglistFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'description' => $this->faker->words(5, true),
            'displayName' => $this->faker->words(5, true),
            'fqdnListname' => $this->faker->safeEmail(),
            'listId' => $this->faker->domainName(),
            'listName' => $this->faker->words(5, true),
            'mailHost' => $this->faker->domainName(),
            'memberCount' => $this->faker->numberBetween(10, 100),
            'selfLink' => $this->faker->url(),
            'volume' => 1,
        ];
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function toData(array $attributes = []): MailingList
    {
        return MailingList::from($this->create($attributes));
    }

    public function id(string $id): self
    {
        return $this->state([
            'list_id' => $id,
        ]);
    }
}
