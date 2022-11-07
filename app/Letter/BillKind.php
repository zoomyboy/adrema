<?php

namespace App\Letter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillKind extends Model
{
    use HasFactory;

    public $fillable = ['name'];
    public $timestamps = false;
}
