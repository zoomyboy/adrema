<?php

namespace App\Form\Casts;

use App\Form\Data\FieldCollection;
use App\Form\Fields\Field;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class FieldCollectionCast implements Cast
{
    /**
     * @param array<int, array<string, string>> $value
     * @return FieldCollection
     */
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        return new FieldCollection(collect($value)->map(fn ($value) => Field::classFromType($value['type'])::from($value))->all());
    }
}
