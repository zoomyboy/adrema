<?php

namespace App\Lib\Editor;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class Condition extends Data
{

    /** @param DataCollection<int, Statement> $ifs */
    public function __construct(
        public ConditionMode $mode,
        #[DataCollectionOf(Statement::class)]
        public DataCollection $ifs,
    ) {
    }

    public static function fromMedia(Media $media): self
    {
        return static::withoutMagicalCreationFrom($media->getCustomProperty('conditions') ?: [
            'mode' => 'any',
            'ifs' => [],
        ]);
    }

    /**
     * @param array<string, mixed> $block
     */
    public static function fromBlock(array $block): self
    {

        return static::withoutMagicalCreationFrom([
            'mode' => data_get($block, 'tunes.condition.mode', 'any'),
            'ifs' =>  data_get($block, 'tunes.condition.ifs', []),
        ]);
    }

    public function hasStatements(): bool
    {
        return count($this->ifs) > 0;
    }

    public function isAny(): bool
    {
        return $this->mode === ConditionMode::ANY;
    }

    public function isAll(): bool
    {
        return $this->mode === ConditionMode::ALL;
    }
}
