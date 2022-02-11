<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Confession extends Model
{

    use HasFactory;

    public $fillable = ['name', 'nami_id', 'is_null'];
    public $timestamps = false;
}
