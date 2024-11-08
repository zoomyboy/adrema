<?php

namespace App\Form\Casts;

use App\Form\Fields\Field;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class CollectionCast implements Cast
{

    /**
     * @param class-string<Data> $target
     */
    public function __construct(public string $target)
    {
    }

    /**
     * @param array<int, array<string, mixed>> $value
     * @return Collection<int, Data>
     */
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        return collect($value)->map(fn ($item) => $this->target::from($item));
    }
}
