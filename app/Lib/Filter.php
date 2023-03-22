<?php

namespace App\Lib;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

/**
 * @template T of Model
 */
abstract class Filter extends Data
{
    public string $unsetReplacer = 'yoNee3ainge4eetiier9ogaiChoe0ahcaR3Hu1uzah8xaiv7ael7yahphai7ruG9';

    /**
     * @return array<string, mixed>
     */
    abstract protected function locks(): array;

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
        return static::withoutMagicalCreationFrom($post ?: [])->parseLocks();
    }

    public function parseLocks(): static
    {
        foreach ($this->locks() as $key => $value) {
            if ($value === $this->unsetReplacer) {
                continue;
            }

            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function when(bool $when, $value)
    {
        return $when ? $value : $this->unsetReplacer;
    }

    /**
     * @param Builder<T> $query
     *
     * @return Builder<T>
     */
    protected function applyOwnOthers(Builder $query, bool $own, bool $others): Builder
    {
        if ($own && !$others) {
            $query->where('user_id', auth()->id());
        }

        if (!$own && $others) {
            $query->where('user_id', '!=', auth()->id());
        }

        if (!$own && !$others) {
            $query->where('id', -1);
        }

        return $query;
    }
}
