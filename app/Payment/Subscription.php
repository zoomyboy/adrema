<?php

namespace App\Payment;

use App\Fee;
use Database\Factories\Payment\SubscriptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    public $fillable = ['name', 'fee_id'];

    public function getAmount(): int
    {
        return $this->children->sum('amount');
    }

    /**
     * @return BelongsTo<Fee, $this>
     */
    public function fee(): BelongsTo
    {
        return $this->belongsTo(Fee::class);
    }

    /**
     * @return HasMany<SubscriptionChild, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(SubscriptionChild::class, 'parent_id');
    }

    public static function booted(): void
    {
        static::deleting(fn ($model) => $model->children()->delete());
    }

    /**
     * @return array<int, array{name: string, id: int}>
     */
    public static function forSelect(): array
    {
        return static::select('name', 'id')->get()->toArray();
    }
}
