<?php

namespace App\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    public $fillable = ['name', 'is_bill', 'is_remember'];
    public $timestamps = false;

    public static function default() {
        return static::where('is_bill', true)->where('is_remember', true)->first()->id;
    }
}
