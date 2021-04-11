<?php

namespace App\Bill;

use Illuminate\Database\Eloquent\Model;

class BillKind extends Model
{
    public $fillable = ['name'];
    public $timestamps = false;
}
