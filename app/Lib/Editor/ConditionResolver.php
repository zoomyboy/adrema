<?php

namespace App\Lib\Editor;

abstract class ConditionResolver
{

    abstract public function filterCondition(Condition $condition): bool;

    /**
     * @param array<string, mixed> $content
     * @return array<string, mixed>
     */
    public function makeBlocks(array $content): array
    {
        return array_filter(data_get($content, 'blocks', []), fn ($block) => $this->filterBlock($block));
    }

    /**
     * @param array<string, mixed> $block
     */
    public function filterBlock(array $block): bool
    {
        return $this->filterCondition(Condition::fromBlock($block));
    }
}
