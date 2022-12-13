<?php

namespace App\Payment;

use App\Fee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    public $fillable = ['name', 'amount', 'fee_id'];

    public function getAmount(): int
    {
        return $this->children->sum('amount');
    }

    public function fee(): BelongsTo
    {
        return $this->belongsTo(Fee::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(SubscriptionChild::class, 'parent_id');
    }

    public static function booted(): void
    {
        static::deleting(fn ($model) => $model->children()->delete());
    }
}
