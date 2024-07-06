<?php

namespace Tests\RequestFactories;

use App\Lib\Editor\EditorData;
use Worksome\RequestFactories\RequestFactory;

class EditorRequestFactory extends RequestFactory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'time' => 45069432,
            'blocks' => [
                ['id' => 'TTzz66', 'type' => 'paragraph', 'data' => ['text' => 'lorem']]
            ],
            'version' => '1.0',
        ];
    }

    public function empty(): self
    {
        return $this->state(['blocks' => []]);
    }

    /**
     * @param array<string, mixed> $conditions
     */
    public function text(int $id, string $text, array $conditions = ['mode' => 'all', 'ifs' => []]): self
    {
        return $this->state($this->paragraphBlock($id, $text, $conditions));
    }

    /**
     * @param array<string, mixed> $conditions
     * @return array<string, mixed>
     */
    public function paragraphBlock(int $id, string $text, array $conditions = ['mode' => 'all', 'ifs' => []]): array
    {
        return [
            'time' => 1,
            'version' => '1.0',
            'blocks' => [
                [
                    'id' => $id,
                    'type' => 'paragraph',
                    'data' => ['text' => $text],
                    'tunes' => [
                        'condition' => $conditions
                    ]
                ]
            ],
        ];
    }

    /**
     * @param array<int, string> $paragraphs
     */
    public function paragraphs(array $paragraphs): self
    {
        return $this->state([
            'time' => 1,
            'version' => '1.0',
            'blocks' => collect($paragraphs)->map(fn ($paragraph) => [
                'id' => $this->faker->numberBetween([0, 10000]),
                'type' => 'paragraph',
                'data' => ['text' => $paragraph],
                'tunes' => [
                    'condition' => ['mode' => 'all', 'ifs' => []]
                ]
            ])->toArray(),
        ]);
    }

    public function toData(): EditorData
    {
        return EditorData::from($this->create());
    }
}
