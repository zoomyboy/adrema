<?php

namespace App\Form\Transformers;

use App\Form\Fields\Field;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Transformers\Transformer;

class CollectionTransformer implements Transformer
{

    public function __construct(public string $target)
    {
    }

    /**
     * @param Collection<int, Field> $value
     * @return array<string, mixed>
     */
    public function transform(DataProperty $property, mixed $value): mixed
    {
        return $value->toArray();
    }
}
