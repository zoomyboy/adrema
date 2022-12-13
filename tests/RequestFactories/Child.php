<?php

namespace Tests\RequestFactories;

class Child
{
    public function __construct(private string $name, private int $amount)
    {
    }

    /**
     * @return array{name: string, amount: int}
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'name' => $this->name,
        ];
    }
}
