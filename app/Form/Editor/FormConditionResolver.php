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
    public function filterBlock(array $block): bool
    {
        $mode = data_get($block, 'tunes.condition.mode', 'any');
        $ifs = data_get($block, 'tunes.condition.ifs', []);

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

        if ($mode === 'all') {
            return true;
        }
    }
}
