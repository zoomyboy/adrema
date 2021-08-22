<?php

namespace App\Nami;

use Exception;

trait HasNamiField
{

    public static function nami(int $id): ?self
    {
        $model = static::firstWhere('nami_id', $id);

        if (is_null($model)) {
            throw new Exception('Nami search on '.static::class.' with ID '.$id.' failed.');
        }

        return $model;
    }

}
