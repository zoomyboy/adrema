<?php

namespace App\Payment;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionChild extends Model
{
    use HasFactory;
    use HasUuids;

    public $timestamps = false;

    public $fillable = ['name', 'amount', 'uuid', 'parent_id'];
}
