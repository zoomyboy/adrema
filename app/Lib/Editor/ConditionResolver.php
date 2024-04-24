<?php

namespace App\Lib\Editor;

abstract class ConditionResolver
{

    /**
     * @param array<string, mixed> $ifs
     */
    abstract public function filterCondition(string $mode, array $ifs): bool;

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
        $mode = data_get($block, 'tunes.condition.mode', 'any');
        $ifs = data_get($block, 'tunes.condition.ifs', []);

        return $this->filterCondition($mode, $ifs);
    }
}
