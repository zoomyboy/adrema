<?php

namespace Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;
use App\Lib\Editor\Condition;

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
                ['field' => $field, 'value' => $value, 'comparator' => 'isEqual']
            ],
        ]);
    }

    public function toData(): Condition {
        return Condition::from($this->create());
    }
}
