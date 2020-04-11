<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Confession extends Model
{
    public $fillable = ['name', 'nami_id', 'is_null'];

    public $timestamps = false;
}
