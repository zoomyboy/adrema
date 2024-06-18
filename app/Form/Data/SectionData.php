<?php

namespace App\Form\Data;

use App\Form\Casts\FieldCollectionCast;
use Spatie\LaravelData\Data;
use App\Form\Transformers\FieldCollectionTransformer;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;

class SectionData extends Data
{

    public function __construct(
        public string $name,
        #[WithCast(FieldCollectionCast::class)]
        #[WithTransformer(FieldCollectionTransformer::class)]
        public FieldCollection $fields,
        public ?string $intro
    ) {
    }
}
