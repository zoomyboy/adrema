<?php

namespace App;

use App\Nami\HasNamiField;
use Database\Factories\NationalityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    /** @use HasFactory<NationalityFactory> */
    use HasFactory;
    use HasNamiField;

    public $fillable = ['name', 'nami_id'];
}
