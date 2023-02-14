<?php

namespace App\Nami;

use Illuminate\Database\Eloquent\Builder;

trait HasNamiField
{
    public static function nami(int $id): ?self
    {
        return static::firstWhere('nami_id', $id);
    }

    public function getHasNamiAttribute(): bool
    {
        return null !== $this->nami_id;
    }

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public static function scopeLocal(Builder $query): Builder
    {
        return $query->whereNull('nami_id');
    }
}
