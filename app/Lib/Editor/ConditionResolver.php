<?php

namespace App\Lib\Editor;

abstract class ConditionResolver
{

    /**
     * @param array<string, mixed> $block
     */
    abstract public function filterBlock(array $block): bool;

    /**
     * @param array<string, mixed> $content
     * @return array<string, mixed>
     */
    public function make(array $content): array
    {
        return array_filter($content['blocks'], fn ($block) => $this->filterBlock($block));
    }
}
