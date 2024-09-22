<?php

namespace App;

use Database\Factories\ConfessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Confession extends Model
{
    /** @use HasFactory<ConfessionFactory> */
    use HasFactory;

    public $fillable = ['name', 'nami_id', 'is_null'];
    public $timestamps = false;
}
