<?php

namespace App\Fileshare\Models;

use App\Fileshare\ConnectionTypes\ConnectionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fileshare extends Model
{
    use HasFactory;

    public $guarded = [];

    public $casts = [
        'type' => ConnectionType::class,
    ];
}
