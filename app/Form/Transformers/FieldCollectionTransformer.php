<?php

namespace App\Form\Transformers;

use App\Form\Fields\Field;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

class FieldCollectionTransformer implements Transformer
{

    /**
     * @param Collection<int, Field> $value
     * @return array<string, mixed>
     */
    public function transform(DataProperty $property, mixed $value, TransformationContext $context): mixed
    {
        return $value->map(fn ($field) => [
            ...$field->toArray(),
            'type' => class_basename($field),
        ])->toArray();
    }
}
