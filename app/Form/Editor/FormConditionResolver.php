<?php

namespace App\Form\Editor;

use App\Form\Models\Participant;
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
    public function filterCondition(string $mode, array $ifs): bool
    {
        if (count($ifs) === 0) {
            return true;
        }

        foreach ($ifs as $if) {
            $field = $this->participant->getFields()->findByKey($if['field']);
            $matches = $field->matches($if['comparator'], $if['value']);
            if ($matches && $mode === 'any') {
                return true;
            }
            if (!$matches && $mode === 'all') {
                return false;
            }
        }

        if ($mode === 'any') {
            return false;
        }

        return true;
    }
}
