<?php

namespace App\Lib;

use Spatie\LaravelData\PaginatedDataCollection;

/** @mixin \Illuminate\Http\Resources\Json\JsonResource */
trait HasMeta
{
    /**
     * Create a new anonymous resource collection.
     *
     * @param mixed $resource
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collection($resource)
    {
        $meta = self::meta();

        if (!count($meta)) {
            return parent::collection($resource);
        }

        return parent::collection($resource)->additional([
            'meta' => $meta,
        ]);
    }

    /**
     * Create a new anonymous resource collection without meta.
     *
     * @param mixed $resource
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collectionWithoutMeta($resource)
    {
        return parent::collection($resource);
    }

    public static function meta(): array
    {
        return [];
    }

    public static function collectPages(mixed $items): array {
        $source = parent::collect($items, PaginatedDataCollection::class)->toArray();
        return [
            ...parent::collect($items, PaginatedDataCollection::class)->toArray(),
            'meta' => [...$source['meta'], ...static::meta()]
        ];
    }
}
