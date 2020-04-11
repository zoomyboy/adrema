<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    public $fillable = ['name', 'is_null', 'nami_id'];

    public $casts = [
        'is_null' => 'boolean'
    ];
}
