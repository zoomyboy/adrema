<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public $timestamps = false;

    public $fillable = ['name', 'nami_id', 'is_null'];

    public $casts = [
        'is_null' => 'boolean'
    ];
}
