<?php

namespace App\Lib\Data;

use Spatie\LaravelData\Normalizers\Normalizer;
use App\Lib\Normalizers\DateNormalizer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\WithTransformer;
use App\Lib\Transformers\DateTransformer;
use Carbon\Carbon;

class DateData extends Data
{
    public function __construct(
        #[WithTransformer(DateTransformer::class)]
        public Carbon $raw,
        public string $human,
    ) {}

    /**
     * @return array<int, class-string<Normalizer>>
     */
    public static function normalizers(): array
    {
        return [
            DateNormalizer::class,
        ];
    }
}
