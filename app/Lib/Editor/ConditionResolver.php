<?php

namespace App\Lib\Editor;

abstract class ConditionResolver
{

    abstract public function filterCondition(Condition $condition): bool;

    /**
     * @return array<int, mixed>
     */
    public function makeBlocks(EditorData $data): array
    {
        return array_filter($data->blocks, fn ($block) => $this->filterBlock($block));
    }

    /**
     * @param array<string, mixed> $block
     */
    public function filterBlock(array $block): bool
    {
        return $this->filterCondition(Condition::factory()->withoutMagicalCreation()->from([
            'mode' => data_get($block, 'tunes.condition.mode', 'any'),
            'ifs' =>  data_get($block, 'tunes.condition.ifs', []),
        ]));
    }
}
