<?php

namespace App\Lib\Transformers;

use Spatie\LaravelData\Transformers\Transformer;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Carbon\Carbon;

class DateTransformer implements Transformer
{
    public function transform(DataProperty $property, mixed $value, TransformationContext $context): string
    {
        return Carbon::parse($value)->format('Y-m-d');
    }
}
