<?php

namespace App\Form\Matchers;

class SingleValueMatcher extends Matcher
{

    public function matches(string $comparator, mixed $value): bool
    {
        if ($comparator === 'isEqual' && $value === $this->value) {
            return true;
        }

        if ($comparator === 'isNotEqual' && $value !== $this->value) {
            return true;
        }

        if ($comparator === 'isIn' && in_array($this->value, $value)) {
            return true;
        }

        if ($comparator === 'isNotIn' && !in_array($this->value, $value)) {
            return true;
        }

        return false;
    }
}
