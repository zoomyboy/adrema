<?php

namespace App\Lib;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Builder;
use Spatie\LaravelData\Data;

/**
 * @template T of Model
 * @property Builder $query
 */
abstract class Filter extends Data
{

    /**
     * @return self<T>
     */
    abstract public function getQuery(): Builder;
    protected Builder $query;

    /**
     * @param array<string, mixed>|string|null $request
     */
    public static function fromRequest(array|string|null $request = null): static
    {
        $payload = is_string($request)
            ? json_decode(rawurldecode(base64_decode($request)), true)
            : $request;

        return static::fromPost($payload);
    }

    /**
     * @param array<string, mixed> $post
     */
    public static function fromPost(?array $post = null): static
    {
        return static::withoutMagicalCreationFrom($post ?: []);
    }
}
