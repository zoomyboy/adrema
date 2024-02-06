<?php

namespace App\Form\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    public $guarded = [];

    public $casts = [
        'data' => 'json',
    ];
}
