<?php

namespace App\Form\Matchers;

abstract class Matcher
{

    public mixed $value;

    public function setValue(mixed $value): self
    {
        $this->value = $value;

        return $this;
    }

    abstract public function matches(string $comparator, mixed $value): bool;
}
