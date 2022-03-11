<?php

namespace App;

use App\Nami\HasNamiField;
use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    use HasNamiField;

    public $fillable = ['name', 'nami_id'];
}
