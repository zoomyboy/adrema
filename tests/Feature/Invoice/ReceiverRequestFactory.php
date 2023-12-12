<?php

namespace Tests\Feature\Invoice;

use Worksome\RequestFactories\RequestFactory;

class ReceiverRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => 'Familie Blabla',
            'address' => 'Musterstr 44',
            'zip' => '22222',
            'location' => 'Solingen',
        ];
    }

    public function name(string $name): self
    {
        return $this->state(['name' => $name]);
    }

    public function address(string $address): self
    {
        return $this->state(['address' => $address]);
    }

    public function zip(string $zip): self
    {
        return $this->state(['zip' => $zip]);
    }

    public function location(string $location): self
    {
        return $this->state(['location' => $location]);
    }
}
