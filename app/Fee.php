<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    public $fillable = ['name', 'nami_id'];
    public $timestamps = false;
}
