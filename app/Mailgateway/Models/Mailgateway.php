<?php

namespace App\Mailgateway\Models;

use App\Mailgateway\Casts\TypeCast;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mailgateway extends Model
{
    use HasFactory;
    use HasUuids;

    public $casts = ['type' => TypeCast::class];
    public $guarded = [];
}
