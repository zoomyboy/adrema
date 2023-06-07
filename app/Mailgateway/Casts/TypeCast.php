<?php

namespace App\Mailgateway\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class TypeCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed                               $value
     *
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $value = json_decode($value, true);

        return app($value['cls'])->setParams($value['params']);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed                               $value
     *
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return json_encode($value);
    }
}
