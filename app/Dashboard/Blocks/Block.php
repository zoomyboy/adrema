<?php

namespace App\Dashboard\Blocks;

abstract class Block
{
    /**
     * @return array<array-key, mixed>
     */
    abstract protected function data(): array;

    abstract protected function title(): string;

    abstract protected function component(): string;

    /**
     * @return array{data: array<array-key, mixed>, title: string, component: string}
     */
    public function render(): array
    {
        return [
            'data' => $this->data(),
            'title' => $this->title(),
            'component' => $this->component(),
        ];
    }
}
