<?php

namespace App\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Fee;

class Subscription extends Model
{
    use HasFactory;

    public $fillable = ['name', 'amount', 'fee_id'];

    public function fee() {
        return $this->belongsTo(Fee::class);
    }
}
