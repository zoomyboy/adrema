<?php

namespace App\Form\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formtemplate extends Model
{
    use HasFactory;

    public $guarded = [];

    public $casts = [
        'config' => 'json',
    ];
}
