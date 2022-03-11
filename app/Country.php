<?php

namespace App;

use App\Nami\HasNamiField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    use HasNamiField;

    public $fillable = ['name', 'nami_id'];

    public static function default(): int
    {
        return self::whereName('Deutschland')->firstOrFail()->id;
    }

    public $timestamps = false;
}
