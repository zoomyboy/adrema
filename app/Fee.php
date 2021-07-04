<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Member\Member;
use App\Payment\Subscription;

class Fee extends Model
{
    use HasFactory;

    public $fillable = ['name', 'nami_id'];
    public $timestamps = false;


    public function subscriptions() {
        return $this->hasMany(Subscription::class);
    }
}
