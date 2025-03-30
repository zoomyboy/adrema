<?php

namespace App\Lib\Editor;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Support\EloquentCasts\DataEloquentCast;

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
        return $media->getCustomProperty('conditions') ? static::factory()->withoutMagicalCreation()->from($media->getCustomProperty('conditions')) : static::defaults();
    }

    public static function defaults(): self
    {
        return static::factory()->withoutMagicalCreation()->from(['mode' => 'any', 'ifs' => []]);
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

    /**
     * @param array<int, mixed> $arguments
     * @return DataEloquentCast<self>
     */
    public static function castUsing(array $arguments): DataEloquentCast
    {
        return new DataEloquentCast(static::class, $arguments);
    }
}
