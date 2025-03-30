<?php

namespace App\Form\Data;

use App\Form\Casts\CollectionCast;
use App\Form\Transformers\CollectionTransformer;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Support\EloquentCasts\DataEloquentCast;

class FormConfigData extends Data
{

    /**
     * @param Collection<int, SectionData> $sections
     */
    public function __construct(
        #[WithCast(CollectionCast::class, target: SectionData::class)]
        #[WithTransformer(CollectionTransformer::class, target: SectionData::class)]
        public Collection $sections
    ) {
    }

    public function fields(): FieldCollection
    {
        return $this->sections->reduce(
            fn ($carry, $current) => $carry->merge($current->fields->all()),
            new FieldCollection([])
        );
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
