<?php

namespace App;

use App\Payment\Subscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fee extends Model
{
    use HasFactory;

    public $fillable = ['name', 'nami_id'];
    public $timestamps = false;

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
