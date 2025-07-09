<?php

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class ConditionRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'mode' => 'all',
            'ifs' => [],
        ];
    }

    public function whenField(string $field, string $value): self {
        return $this->state([
            'ifs' => [
                ['field' => $field, 'comparator' => 'isEqual', 'value' => $value]
            ],
        ]);
    }
}
