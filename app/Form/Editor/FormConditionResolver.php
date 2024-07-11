<?php

namespace App\Form\Editor;

use App\Form\Models\Participant;
use App\Lib\Editor\Condition;
use App\Lib\Editor\ConditionResolver;

class FormConditionResolver extends ConditionResolver
{

    private Participant $participant;

    public function forParticipant(Participant $participant): self
    {
        $this->participant = $participant;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function filterCondition(Condition $condition): bool
    {
        if (!$condition->hasStatements()) {
            return true;
        }

        foreach ($condition->ifs as $if) {
            $field = $this->participant->getFields()->findByKey($if->field);
            $matches = $field->matches($if->comparator, $if->value);
            if ($matches && $condition->isAny()) {
                return true;
            }
            if (!$matches && $condition->isAll()) {
                return false;
            }
        }

        if ($condition->isAny()) {
            return false;
        }

        return true;
    }
}
