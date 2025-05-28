<?php

namespace App\Lib\Editor;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\EloquentCasts\DataEloquentCast;

/** @todo replace blocks with actual block data classes */
class EditorData extends Data implements Editorable
{

    /** @param array<int, mixed> $blocks */
    public function __construct(
        public string $version,
        public array $blocks,
        public int $time
    ) {}

    public function placeholder(string $search, string $replacement): self
    {
        $replacedBlocks = str(json_encode($this->blocks))->replace('{' . $search . '}', $replacement);
        $this->blocks = json_decode($replacedBlocks, true);

        return $this;
    }

    /**
     * @param array<int, string> $wanted
     */
    public function hasAll(array $wanted): bool
    {
        return collect($wanted)->doesntContain(fn($search) => !str(json_encode($this->blocks))->contains($search));
    }

    public function hasNot(string $should): bool
    {
        return !str(json_encode($this->blocks))->contains($should);
    }

    public static function default(): self
    {
        return static::from([
            'version' => '1.0',
            'blocks' => [],
            'time' => 0,
        ]);
    }

    public function append(Editorable $editorable): self
    {
        $this->blocks = array_merge($this->blocks, $editorable->toEditorData()->blocks);

        return $this;
    }

    /**
     * @param array<int, string> $replacements
     */
    public function replaceWithList(string $blockContent, array $replacements): self
    {
        $this->blocks = collect($this->blocks)->map(function ($block) use ($blockContent, $replacements) {
            if (data_get($block, 'type') !== 'paragraph') {
                return $block;
            }

            if (data_get($block, 'data.text') === '{' . $blockContent . '}') {
                return [
                    ...((array)$block),
                    'type' => 'list',
                    'data' => [
                        'style' => 'unordered',
                        'items' => collect($replacements)->map(fn($replacement) => [
                            'content' => $replacement,
                            'items' => [],
                        ]),
                    ]
                ];
            }

            return $block;
        })->toArray();


        return $this;
    }

    public function toEditorData(): EditorData
    {
        return $this;
    }

    /**
     * @param array<int, mixed> $arguments
     * @return DataEloquentCast<self>
     */
    public static function castUsing(array $arguments): DataEloquentCast
    {
        return new DataEloquentCast(static::class, $arguments);
    }
}
