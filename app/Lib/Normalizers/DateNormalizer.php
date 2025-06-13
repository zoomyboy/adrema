<?php

namespace App\Lib\Normalizers;

use Spatie\LaravelData\Normalizers\Normalizer;
use Carbon\Carbon;

class DateNormalizer implements Normalizer
{
    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $value): ?array
    {
        if (!$value instanceof Carbon) {
            return null;
        }

        return [
            'raw' => $value,
            'human' => $value->format('d.m.Y'),
        ];
    }
}
