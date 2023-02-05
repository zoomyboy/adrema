<?php

namespace App\Nami;

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
}
