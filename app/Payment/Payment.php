<?php

namespace App\Payment;

use App\Member\Member;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    public $fillable = ['member_id', 'subscription_id', 'nr', 'status_id', 'last_remembered_at'];

    /**
     * @return BelongsTo<Member, self>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * @return BelongsTo<Subscription, self>
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * @return BelongsTo<Status, self>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeWhereNeedsPayment(Builder $query): Builder
    {
        return $query->whereHas('status', function ($q) {
            return $q->needsPayment();
        });
    }

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeWhereNeedsBill(Builder $query): Builder
    {
        return $query->whereHas('status', function ($q) {
            return $q->where('is_bill', true);
        });
    }

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeWhereNeedsRemember(Builder $query): Builder
    {
        return $query->whereHas('status', function ($q) {
            return $q->where('is_remember', true);
        })->where(fn ($query) => $query->whereNull('last_remembered_at')->orWhere('last_remembered_at', '<=', now()->subMonths(3)));
    }
}
