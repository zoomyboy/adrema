<?php

namespace App\Maildispatcher\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localmaildispatcher extends Model
{
    use HasFactory;
    use HasUuids;

    public $guarded = [];

    public $timestamps = false;
}
