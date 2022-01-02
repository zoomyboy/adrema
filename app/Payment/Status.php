<?php

namespace App\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    public $fillable = ['name', 'is_bill', 'is_remember'];
    public $timestamps = false;
    public $casts = [
        'is_bill' => 'boolean',
        'is_remember' => 'boolean',
    ];


    public static function default() {
        return static::where('is_bill', true)->where('is_remember', true)->first()->id;
    }

    public function isAccepted(): bool
    {
        return $this->is_bill === false && $this->is_remember === false;
    }

    // ---------------------------------- Scopes -----------------------------------
    public function scopeNeedsPayment($q) {
        return $q->where(function($q) {
            $q->where('is_bill', true)->orWhere('is_remember', true);
        });
    }
}
