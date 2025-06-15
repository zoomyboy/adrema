<?php

namespace App\Lib;

use Spatie\LaravelData\PaginatedDataCollection;

/**
 * @mixin Spatie\LaravelData\Data
 */
trait HasDataMeta
{
    /**
     * @return array<string, mixed>
     */
    public static function collectPages(mixed $items): array {
        $source = parent::collect($items, PaginatedDataCollection::class)->toArray();
        return [
            ...parent::collect($items, PaginatedDataCollection::class)->toArray(),
            'meta' => [...$source['meta'], ...static::meta()]
        ];
    }
}
