<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    public $fillable = ['title', 'nami_id'];
    public $timestamps = false;
}
