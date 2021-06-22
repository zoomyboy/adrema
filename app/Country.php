<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    public $fillable = ['name', 'nami_id'];

    public static function default(): int {
        return self::whereName('Deutschland')->firstOrFail()->id;
    }

    public $timestamps = false;
}
