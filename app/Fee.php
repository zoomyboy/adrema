<?php

namespace App;

use App\Nami\HasNamiField;
use App\Payment\Subscription;
use Database\Factories\FeeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fee extends Model
{
    /** @use HasFactory<FeeFactory> */
    use HasFactory;
    use HasNamiField;

    public $fillable = ['name', 'nami_id'];

    /** @var bool */
    public $timestamps = false;

    /**
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
