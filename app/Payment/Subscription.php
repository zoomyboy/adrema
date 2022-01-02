<?php

namespace App\Payment;

use App\Fee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    public $fillable = ['name', 'amount', 'fee_id'];

    public function fee(): BelongsTo {
        return $this->belongsTo(Fee::class);
    }
}
