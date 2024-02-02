<?php

namespace Tests\RequestFactories;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function text(int $id, string $text): self
    {
        return $this->state($this->paragraphBlock($id, $text));
    }

    public function paragraphBlock(int $id, string $text): array
    {
        return [
            'time' => 1,
            'version' => '1.0',
            'blocks' => [
                ['id' => $id, 'type' => 'paragraph', 'data' => ['text' => $text]]
            ],
        ];
    }
}
