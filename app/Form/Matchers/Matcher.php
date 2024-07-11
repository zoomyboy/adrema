<?php

namespace App\Form\Matchers;

use App\Lib\Editor\Comparator;

abstract class Matcher
{

    public mixed $value;

    public function setValue(mixed $value): self
    {
        $this->value = $value;

        return $this;
    }

    abstract public function matches(Comparator $comparator, mixed $value): bool;
}
