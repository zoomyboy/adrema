<?php

namespace App\Form\Matchers;

use App\Lib\Editor\Comparator;

class SingleValueMatcher extends Matcher
{

    public function matches(Comparator $comparator, mixed $value): bool
    {
        if ($comparator === Comparator::EQUAL && $value === $this->value) {
            return true;
        }

        if ($comparator === Comparator::NOTEQUAL && $value !== $this->value) {
            return true;
        }

        if ($comparator === Comparator::IN && in_array($this->value, $value)) {
            return true;
        }

        if ($comparator === Comparator::NOTIN && !in_array($this->value, $value)) {
            return true;
        }

        return false;
    }
}
