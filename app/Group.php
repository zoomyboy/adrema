<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    public $fillable = ['nami_id', 'name'];
    public $timestamps = false;

    public static function nami($id) {
        return static::firstWhere('nami_id', $id);
    }
}
